<?php

namespace App\Livewire\Components\Messaging;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\ConversationMessage;
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
    public function loadConversation($id)
    {
        $this->conversationId = $id;
        $this->isOpen = true;
        $user = Auth::user();

        if (is_numeric($id)) {
            $conv = Conversation::with(['participants.user', 'messages.sender', 'contextable'])->find($id);

            if ($conv) {
                // Ensure current user is a participant
                if ($user && ! $conv->participants->contains('user_id', $user->id)) {
                    $this->isOpen = false;
                    session()->flash('warning', 'Unauthorized access to conversation.');
                    return;
                }

                $peer = $conv->getOtherParticipant($user);
                $this->recipientId = $peer?->id;
                $this->recipientName = $conv->getOtherPartyName($user);
                $this->avatarLetter = strtoupper(substr($this->recipientName, 0, 1));
                $this->itemTitle = $conv->contextable?->title ?: ($conv->isSupport() ? 'Official Support Desk' : 'Discussion Inquiry');

                // Mark unread messages sent by peer as read
                if ($user) {
                    ConversationMessage::where('conversation_id', $conv->id)
                        ->where('sender_id', '!=', $user->id)
                        ->whereNull('read_at')
                        ->update(['read_at' => now()]);

                    // Dispatch event so message badge updates
                    $this->dispatch('messages-marked-read');
                }

                // Load messages in entry order (chronological)
                $this->messages = $conv->messages->sortBy('created_at')->values()->map(function ($msg) use ($user) {
                    $isMe = $user && ($msg->sender_id === $user->id);
                    return [
                        'id' => $msg->id,
                        'sender' => $isMe ? 'me' : 'them',
                        'text' => $msg->body,
                        'time' => $msg->created_at ? $msg->created_at->diffForHumans() : 'Just now',
                    ];
                })->toArray();

                return;
            }
        }

        // Demo sample fallback
        if ($id === 'abel') {
            $this->recipientName = 'Abel Electronics Hub';
            $this->itemTitle = 'Dell Latitude 5420 Motherboard';
            $this->avatarLetter = 'A';
            $this->messages = [
                ['sender' => 'them', 'text' => 'Can you confirm if your model uses the i5 11th Gen processor?', 'time' => '24m ago'],
                ['sender' => 'me', 'text' => 'Yes, it is the Core i5 11th Gen board.', 'time' => '20m ago'],
            ];
        } elseif ($id === 'seth') {
            $this->recipientName = 'Seth Repair Yard';
            $this->itemTitle = 'HP EliteBook 840 Battery';
            $this->avatarLetter = 'S';
            $this->messages = [
                ['sender' => 'them', 'text' => 'Yes, I have 3 tested battery units available for pickup in Ikeja.', 'time' => '1h ago'],
            ];
        } else {
            $this->recipientName = 'Adam Computers';
            $this->itemTitle = 'HP EliteBook 840 G5';
            $this->avatarLetter = 'A';
            $this->messages = [
                ['sender' => 'them', 'text' => 'I can accept ₦480,000 if you can arrange pickup.', 'time' => '2:14 PM'],
                ['sender' => 'me', 'text' => 'Can I test the laptop before payment?', 'time' => '2:17 PM'],
                ['sender' => 'them', 'text' => 'Yes. You can test it at my location before completing purchase.', 'time' => '2:19 PM'],
            ];
        }
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