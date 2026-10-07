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

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            // Find existing support conversation or ensure one exists
            $supportConv = Conversation::where('contextable_type', User::class)
                ->where('contextable_id', $user->id)
                ->first();

            if ($supportConv) {
                $this->activeConversationId = $supportConv->id;
                $this->markAsRead($supportConv->id);
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

        ConversationMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'body' => trim($this->messageText),
            'read_at' => null,
        ]);

        $conversation->touch();
        $this->messageText = '';
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

        // Query user's conversations: support conversation + any participated conversations
        $conversations = Conversation::query()
            ->where(function ($q) use ($userId) {
                $q->where(function ($sub) use ($userId) {
                    $sub->where('contextable_type', User::class)
                        ->where('contextable_id', $userId);
                })->orWhereHas('participants', function ($p) use ($userId) {
                    $p->where('user_id', $userId);
                });
            })
            ->with(['contextable', 'latestMessage.sender'])
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
                ?? Conversation::with('contextable')->find($this->activeConversationId);

            if ($activeConversation) {
                $activeMessages = ConversationMessage::with('sender')
                    ->where('conversation_id', $activeConversation->id)
                    ->orderBy('created_at', 'asc')
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
