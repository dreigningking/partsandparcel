<?php

namespace App\Livewire\Dashboard\Messages;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class MessageConversation extends Component
{
    public $conversationId = null;
    public ?Conversation $conversation = null;
    public string $messageText = '';
    public string $otherPartyName = 'Vendor';
    public string $avatarLetter = 'V';
    public string $contextTitle = 'Discussion Inquiry';
    public bool $isSupport = false;
    public array $messages = [];

    public function getListeners()
    {
        $userId = Auth::id();
        $listeners = [];

        if ($userId) {
            $listeners["echo-private:user.{$userId},MessageSent"] = 'onMessageReceived';
        }

        if ($this->conversationId && is_numeric($this->conversationId)) {
            $listeners["echo-private:conversation.{$this->conversationId},MessageSent"] = 'onMessageReceived';
        }

        return $listeners;
    }

    public function onMessageReceived($payload = null)
    {
        if (! $payload) {
            return;
        }

        if (isset($payload['sender_id']) && Auth::check() && (int) $payload['sender_id'] === (int) Auth::id()) {
            return;
        }

        if (isset($payload['conversation_id']) && (int) $payload['conversation_id'] === (int) $this->conversationId) {
            if (! empty($payload['id']) && collect($this->messages)->contains('id', $payload['id'])) {
                return;
            }

            $this->messages[] = [
                'id' => $payload['id'] ?? rand(1000, 9999),
                'sender' => 'them',
                'sender_name' => $payload['sender_name'] ?? $this->otherPartyName,
                'body' => $payload['body'] ?? ($payload['text'] ?? ''),
                'time' => $payload['time'] ?? 'Just now',
                'date' => 'Today',
            ];

            if (! empty($payload['id'])) {
                ConversationMessage::where('id', $payload['id'])->update(['read_at' => now()]);
                $this->dispatch('messages-marked-read');
            }
        }
    }

    public function mount()
    {
        $this->conversationId = request()->query('conv') ?? request()->query('id', 'adam');
        $this->loadConversation();
    }

    public function loadConversation(): void
    {
        $user = Auth::user();

        if (is_numeric($this->conversationId)) {
            $conv = Conversation::with([
                'participants.user',
                'messages' => fn($q) => $q->orderBy('created_at', 'asc')->orderBy('id', 'asc'),
                'messages.sender',
                'contextable'
            ])->find($this->conversationId);

            if ($conv) {
                if ($user && ! $conv->participants->contains('user_id', $user->id) && ! $user->isAdmin()) {
                    abort(403, 'Unauthorized access to conversation.');
                }

                $this->conversation = $conv;
                $this->isSupport = $conv->isSupport();
                $this->otherPartyName = $conv->getOtherPartyName($user);
                $this->avatarLetter = strtoupper(substr($this->otherPartyName, 0, 1));
                $this->contextTitle = $conv->contextable?->title ?: ($this->isSupport ? 'Official Customer Support Desk' : 'Direct Conversation');

                // Mark unread messages sent by peer as read
                if ($user) {
                    ConversationMessage::where('conversation_id', $conv->id)
                        ->where('sender_id', '!=', $user->id)
                        ->whereNull('read_at')
                        ->update(['read_at' => now()]);
                }

                // Load messages strictly in order of entry (chronological ascending)
                $this->messages = $conv->messages->sortBy('created_at')->values()->map(function ($msg) use ($user) {
                    $isMe = $user && ($msg->sender_id === $user->id);
                    $timeFormatted = $msg->created_at
                        ? ($msg->created_at->diffInHours(now()) >= 24 ? $msg->created_at->format('M j, g:i A') : $msg->created_at->format('g:i A'))
                        : 'Just now';

                    return [
                        'id' => $msg->id,
                        'sender' => $isMe ? 'me' : 'them',
                        'sender_name' => $msg->sender?->business_name ?: $msg->sender?->name ?: ($this->isSupport ? 'Support' : 'Vendor'),
                        'body' => $msg->body,
                        'time' => $timeFormatted,
                        'date' => $msg->created_at ? $msg->created_at->format('M d, Y') : 'Today',
                    ];
                })->toArray();

                return;
            }
        }

        // Demo sample fallback (if conv is string like 'adam', 'abel', etc.)
        $this->otherPartyName = 'Adam Computers';
        $this->avatarLetter = 'A';
        $this->contextTitle = 'HP EliteBook 840 G5 Laptop';
        $this->messages = [
            [
                'id' => 1,
                'sender' => 'them',
                'sender_name' => 'Adam Computers',
                'body' => 'Hi Adam, is this EliteBook 840 G5 still available at your Computer Village shop? I need a clean unit for client work.',
                'time' => '1:45 PM',
                'date' => 'Today',
            ],
            [
                'id' => 2,
                'sender' => 'me',
                'sender_name' => 'You',
                'body' => 'Hello! Yes, it is fully tested and available at Stall 14, Otigba Street, opposite Slot.',
                'time' => '1:48 PM',
                'date' => 'Today',
            ],
            [
                'id' => 3,
                'sender' => 'me',
                'sender_name' => 'You',
                'body' => 'It comes with clean original HP charger and 14-day warranty for testing.',
                'time' => '1:49 PM',
                'date' => 'Today',
            ],
            [
                'id' => 4,
                'sender' => 'them',
                'sender_name' => 'Adam Computers',
                'body' => 'Great! Can you accept ₦260,000 if I come for pickup this afternoon?',
                'time' => '2:10 PM',
                'date' => 'Today',
            ],
            [
                'id' => 5,
                'sender' => 'me',
                'sender_name' => 'You',
                'body' => 'I can accept ₦260,000 if you can pick up today at Stall 14 before 5 PM.',
                'time' => '2:14 PM',
                'date' => 'Today',
            ],
        ];
    }

    public function sendMessage(): void
    {
        $body = trim($this->messageText);
        if ($body === '') {
            return;
        }

        $user = Auth::user();
        if (! $user) {
            return;
        }

        if ($this->conversation) {
            $msg = ConversationMessage::create([
                'conversation_id' => $this->conversation->id,
                'sender_id' => $user->id,
                'body' => $body,
                'read_at' => null,
            ]);

            $this->conversation->touch();

            try {
                $recipient = $this->conversation->getOtherParticipant($user);
                $pendingBroadcast = broadcast(new MessageSent($msg, $recipient?->id));
                unset($pendingBroadcast);
            } catch (\Throwable $e) {
                logger()->warning('Realtime broadcast skipped: ' . $e->getMessage());
            }

            $this->messages[] = [
                'id' => $msg->id,
                'sender' => 'me',
                'sender_name' => $user->business_name ?: $user->name,
                'body' => $msg->body,
                'time' => 'Just now',
                'date' => 'Today',
            ];

            $this->messageText = '';
            return;
        }

        // Demo sample fallback
        $this->messages[] = [
            'id' => rand(1000, 9999),
            'sender' => 'me',
            'sender_name' => $user->business_name ?: $user->name,
            'body' => $body,
            'time' => 'Just now',
            'date' => 'Today',
        ];
        $this->messageText = '';
    }

    public function render()
    {
        return view('livewire.dashboard.messages.message-conversation');
    }
}