<?php

namespace App\Livewire\Components\Messaging;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\ConversationParticipant;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ConversationDrawer extends Component
{
    public $conversationId = null;
    public bool $isOpen = false;
    public string $recipientName = 'Vendor';
    public ?int $recipientId = null;
    public string $itemTitle = 'Community Request';
    public string $avatarLetter = 'V';
    public array $messages = [];
    public string $newMessage = '';

    public function getListeners()
    {
        $userId = Auth::id();
        $listeners = [
            'open-conversation' => 'loadConversation',
            'close-conversation' => 'closeDrawer',
        ];

        if ($userId) {
            $listeners["echo-private:user.{$userId},MessageSent"] = 'onMessageReceived';
        }

        if ($this->conversationId && is_numeric($this->conversationId)) {
            $listeners["echo-private:conversation.{$this->conversationId},MessageSent"] = 'onMessageReceived';
        }

        return $listeners;
    }

    #[On('open-conversation')]
    public function loadConversation($id = null, $recipientId = null, $userId = null, $contextType = null, $contextId = null)
    {
        $user = Auth::user();

        if (! $user) {
            $this->isOpen = false;
            session()->flash('warning', 'Please sign in to send messages.');
            return redirect()->route('login');
        }

        // 1. Direct Recipient ID passed (from listing-details, user-profile, etc.)
        $targetRecipientId = $recipientId ?: $userId;
        if ($targetRecipientId && is_numeric($targetRecipientId)) {
            $peer = User::find($targetRecipientId);
            if ($peer) {
                if ((int) $peer->id === (int) $user->id) {
                    $this->isOpen = false;
                    return;
                }
                $conv = $this->resolveConversationForUsers($user, $peer, $contextType, $contextId);
                $this->setLoadedConversation($conv, $user);
                return;
            }
        }

        // 2. Demo sample fallback
        if ($id === 'abel') {
            $this->conversationId = 'abel';
            $this->isOpen = true;
            $this->recipientName = 'Abel Electronics Hub';
            $this->itemTitle = 'Dell Latitude 5420 Motherboard';
            $this->avatarLetter = 'A';
            $this->messages = [
                ['sender' => 'them', 'text' => 'Can you confirm if your model uses the i5 11th Gen processor?', 'time' => '24m ago'],
                ['sender' => 'me', 'text' => 'Yes, it is the Core i5 11th Gen board.', 'time' => '20m ago'],
            ];
            return;
        }

        if ($id === 'seth') {
            $this->conversationId = 'seth';
            $this->isOpen = true;
            $this->recipientName = 'Seth Repair Yard';
            $this->itemTitle = 'HP EliteBook 840 Battery';
            $this->avatarLetter = 'S';
            $this->messages = [
                ['sender' => 'them', 'text' => 'Yes, I have 3 tested battery units available for pickup in Ikeja.', 'time' => '1h ago'],
            ];
            return;
        }

        // 3. Numeric ID passed
        if (is_numeric($id)) {
            // First: Check if $id is an existing Conversation the current user belongs to
            $conv = Conversation::with(['participants.user', 'messages.sender', 'contextable'])->find($id);

            if ($conv && $conv->participants->contains('user_id', $user->id)) {
                $this->setLoadedConversation($conv, $user);
                return;
            }

            // Second: Check if $id was actually passed as a User ID (recipient)
            $peer = User::find($id);
            if ($peer) {
                if ((int) $peer->id === (int) $user->id) {
                    $this->isOpen = false;
                    return;
                }
                $conv = $this->resolveConversationForUsers($user, $peer, $contextType, $contextId);
                $this->setLoadedConversation($conv, $user);
                return;
            }

            // Third: If it's a conversation belonging to other participants
            if ($conv && ! $conv->participants->contains('user_id', $user->id)) {
                $this->isOpen = false;
                session()->flash('warning', 'Unauthorized access to conversation.');
                return;
            }
        }

        // 4. Default demo sample fallback for unknown keys
        $this->conversationId = $id;
        $this->isOpen = true;
        $this->recipientName = 'Adam Computers';
        $this->itemTitle = 'HP EliteBook 840 G5';
        $this->avatarLetter = 'A';
        $this->messages = [
            ['sender' => 'them', 'text' => 'I can accept ₦480,000 if you can arrange pickup.', 'time' => '2:14 PM'],
            ['sender' => 'me', 'text' => 'Can I test the laptop before payment?', 'time' => '2:17 PM'],
            ['sender' => 'them', 'text' => 'Yes. You can test it at my location before completing purchase.', 'time' => '2:19 PM'],
        ];
    }

    protected function setLoadedConversation(Conversation $conv, User $user): void
    {
        $this->conversationId = $conv->id;
        $this->isOpen = true;

        $peer = $conv->getOtherParticipant($user);
        $this->recipientId = $peer?->id;
        $this->recipientName = $conv->getOtherPartyName($user);
        $this->avatarLetter = strtoupper(substr($this->recipientName, 0, 1));

        if ($conv->contextable) {
            $context = $conv->contextable;
            $this->itemTitle = $context->item?->name
                ?? $context->title
                ?? $context->name
                ?? ($context instanceof Listing ? ($context->description ?: 'Listing Inquiry') : 'Direct Inquiry');
        } elseif ($conv->isSupport()) {
            $this->itemTitle = 'Official Support Desk';
        } else {
            $this->itemTitle = 'Direct Inquiry';
        }

        // Mark unread messages sent by peer as read
        ConversationMessage::where('conversation_id', $conv->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Dispatch event so message badge updates
        $this->dispatch('messages-marked-read');

        // Load messages in entry order (chronological)
        $this->messages = $conv->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) use ($user) {
                $isMe = ($msg->sender_id === $user->id);
                return [
                    'id' => $msg->id,
                    'sender' => $isMe ? 'me' : 'them',
                    'text' => $msg->body,
                    'time' => $msg->created_at ? $msg->created_at->diffForHumans() : 'Just now',
                ];
            })
            ->toArray();
    }

    protected function resolveConversationForUsers(User $user, User $peer, ?string $contextType = null, $contextId = null): Conversation
    {
        if ((int) $user->id === (int) $peer->id) {
            throw new \InvalidArgumentException("Cannot create a conversation with oneself.");
        }

        $query = Conversation::whereHas('participants', fn($q) => $q->where('user_id', $user->id))
            ->whereHas('participants', fn($q) => $q->where('user_id', $peer->id));

        $contextableType = null;
        $resolvedContextId = null;

        if ($contextType === 'listing' && $contextId) {
            $contextableType = Listing::class;
            $resolvedContextId = (int) $contextId;

            $existing = (clone $query)
                ->where('contextable_type', Listing::class)
                ->where('contextable_id', $resolvedContextId)
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        // Look for an existing conversation between them
        $existing = (clone $query)->latest('updated_at')->first();
        if ($existing) {
            return $existing;
        }

        // Create new conversation
        $conv = Conversation::create([
            'contextable_type' => $contextableType,
            'contextable_id' => $resolvedContextId,
            'created_by' => $user->id,
        ]);

        ConversationParticipant::create([
            'conversation_id' => $conv->id,
            'user_id' => $user->id,
            'joined_at' => now(),
        ]);

        ConversationParticipant::create([
            'conversation_id' => $conv->id,
            'user_id' => $peer->id,
            'joined_at' => now(),
        ]);

        return $conv;
    }

    public function sendMessage()
    {
        $body = trim($this->newMessage);
        if ($body === '') {
            return;
        }

        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please sign in to send messages.');
            return redirect()->route('login');
        }

        if (is_numeric($this->conversationId)) {
            $conv = Conversation::find($this->conversationId);
            if ($conv) {
                $msg = ConversationMessage::create([
                    'conversation_id' => $conv->id,
                    'sender_id' => $user->id,
                    'body' => $body,
                    'read_at' => null,
                ]);

                // Broadcast real-time event to Reverb safely
                try {
                    $socketId = request()->header('X-Socket-ID');
                    $pendingBroadcast = broadcast(new MessageSent($msg, $this->recipientId));
                    if ($socketId && is_string($socketId) && preg_match('/\A\d+\.\d+\z/', $socketId)) {
                        $pendingBroadcast->toOthers();
                    }
                    // Explicitly destroy PendingBroadcast instance so __destruct() dispatches inside try-catch
                    unset($pendingBroadcast);
                } catch (\Throwable $e) {
                    logger()->warning('Realtime message broadcast skipped or failed: ' . $e->getMessage());
                }

                $this->messages[] = [
                    'id' => $msg->id,
                    'sender' => 'me',
                    'text' => $msg->body,
                    'time' => 'Just now',
                ];

                $this->newMessage = '';
                $this->dispatch('message-sent');
                return;
            }
        }

        // Demo sample fallback
        $this->messages[] = [
            'id' => rand(1000, 9999),
            'sender' => 'me',
            'text' => $body,
            'time' => 'Just now',
        ];
        $this->newMessage = '';
    }

    public function onMessageReceived($payload)
    {
        if (! $payload) {
            return;
        }

        if (isset($payload['sender_id']) && Auth::check() && (int) $payload['sender_id'] === (int) Auth::id()) {
            return;
        }

        // Only append if this drawer has this conversation currently loaded
        if (! $this->conversationId || ! isset($payload['conversation_id']) || (int) $payload['conversation_id'] !== (int) $this->conversationId) {
            return;
        }

        // Deduplicate if already present
        if (! empty($payload['id']) && collect($this->messages)->contains('id', $payload['id'])) {
            return;
        }

        $this->messages[] = [
            'id' => $payload['id'] ?? null,
            'sender' => 'them',
            'text' => $payload['body'] ?? ($payload['text'] ?? ''),
            'time' => $payload['time'] ?? 'Just now',
        ];

        // If open, automatically mark as read
        if ($this->isOpen && ! empty($payload['id'])) {
            ConversationMessage::where('id', $payload['id'])->update(['read_at' => now()]);
            $this->dispatch('messages-marked-read');
        }
    }

    #[On('close-conversation')]
    public function closeDrawer()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.components.messaging.conversation-drawer');
    }
}