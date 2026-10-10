<?php

namespace App\Livewire\Dashboard\Messages;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class MessageList extends Component
{
    public ?int $activeConversationId = null;
    public string $searchQuery = '';
    public string $activeTab = 'all'; // 'all', 'unread', 'support'
    public string $messageText = '';

    public function getListeners()
    {
        $userId = Auth::id();
        $listeners = [
            'messages-marked-read' => '$refresh',
            'message-sent' => '$refresh',
        ];

        if ($userId) {
            $listeners["echo-private:user.{$userId},MessageSent"] = 'onMessageReceived';
        }

        return $listeners;
    }

    public function onMessageReceived($payload = null)
    {
        if ($this->activeConversationId && isset($payload['conversation_id']) && (int) $payload['conversation_id'] === (int) $this->activeConversationId) {
            $this->markAsRead($this->activeConversationId);
            $this->dispatch('messages-marked-read');
        }
    }

    public function mount()
    {
        $convId = request()->query('conv');
        if ($convId && is_numeric($convId)) {
            $user = Auth::user();
            if ($user) {
                $conv = Conversation::where('id', (int) $convId)
                    ->where(function ($q) use ($user) {
                        $q->whereHas('participants', fn($p) => $p->where('user_id', $user->id))
                          ->orWhere(function ($sq) use ($user) {
                              $sq->support()->where('contextable_id', $user->id);
                          });
                    })
                    ->first();

                if ($conv) {
                    $this->activeConversationId = (int) $conv->id;
                    $this->markAsRead((int) $conv->id);
                }
            }
        }
    }

    public function selectConversation($id)
    {
        $this->activeConversationId = (int) $id;
        $this->messageText = '';
        $this->markAsRead((int) $id);
    }

    public function clearConversation()
    {
        $this->activeConversationId = null;
        $this->messageText = '';
    }

    public function sendMessage()
    {
        $this->validate([
            'messageText' => 'required|string|max:3000',
        ]);

        if (! $this->activeConversationId) {
            return;
        }

        $conversation = Conversation::find($this->activeConversationId);
        if (! $conversation) {
            return;
        }

        $msg = ConversationMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'body' => trim($this->messageText),
            'read_at' => null,
        ]);

        $conversation->touch();
        $this->messageText = '';

        try {
            $recipient = $conversation->getOtherParticipant(Auth::user());
            $pendingBroadcast = broadcast(new \App\Events\MessageSent($msg, $recipient?->id));
            unset($pendingBroadcast);
        } catch (\Throwable $e) {
            logger()->warning('Realtime broadcast failed: ' . $e->getMessage());
        }
    }

    protected function markAsRead(int $id)
    {
        ConversationMessage::where('conversation_id', $id)
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        $userId = Auth::id();

        // Query user's conversations: support conversation + any participated conversations (must have messages)
        $query = Conversation::query()
            ->has('messages')
            ->where(function ($q) use ($userId) {
                $q->where(function ($sub) use ($userId) {
                    $sub->support()
                        ->where(function ($sq) use ($userId) {
                            $sq->where('contextable_id', $userId)
                               ->orWhereHas('participants', fn($p) => $p->where('user_id', $userId));
                        });
                })->orWhereHas('participants', function ($p) use ($userId) {
                    $p->where('user_id', $userId);
                });
            });

        if (trim($this->searchQuery) !== '') {
            $term = '%' . trim($this->searchQuery) . '%';
            $query->where(function ($q) use ($term) {
                $q->whereHas('participants.user', function ($uq) use ($term) {
                    $uq->where('name', 'like', $term)
                       ->orWhere('business_name', 'like', $term);
                })->orWhereHas('messages', function ($mq) use ($term) {
                    $mq->where('body', 'like', $term);
                });
            });
        }

        $conversations = $query
            ->with(['contextable', 'latestMessage.sender', 'participants.user'])
            ->withCount([
                'messages as unread_count' => function ($q) use ($userId) {
                    $q->whereNull('read_at')->where('sender_id', '!=', $userId);
                },
            ])
            ->orderByDesc('updated_at')
            ->get();

        $activeConversation = null;
        $activeMessages = collect();

        if ($this->activeConversationId) {
            $activeConversation = $conversations->firstWhere('id', $this->activeConversationId)
                ?? Conversation::with(['contextable', 'participants.user'])->find($this->activeConversationId);

            if ($activeConversation) {
                $activeMessages = ConversationMessage::with('sender')
                    ->where('conversation_id', $activeConversation->id)
                    ->orderBy('created_at', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
            }
        }

        $unreadTotal = $conversations->sum('unread_count');

        return view('livewire.dashboard.messages.message-list', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'activeMessages' => $activeMessages,
            'unreadTotal' => $unreadTotal,
        ]);
    }
}
