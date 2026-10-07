<?php

namespace App\Livewire\Admin;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.dash')]
#[Title('Customer Support Desk — Admin Control Center')]
class AdminSupportConversations extends Component
{
    #[Url(as: 'c')]
    public ?int $selectedConversationId = null;

    public string $search = '';

    public string $replyText = '';

    public function mount(): void
    {
        abort_unless(Auth::user()?->hasPermission('manage_support'), 403, 'Unauthorized. Customer support privileges required.');

        if ($this->selectedConversationId) {
            $this->markConversationAsRead($this->selectedConversationId);
        }
    }

    public function selectConversation(int $id): void
    {
        $this->selectedConversationId = $id;
        $this->replyText = '';
        $this->markConversationAsRead($id);
    }

    public function closeConversation(): void
    {
        $this->selectedConversationId = null;
        $this->replyText = '';
    }

    public function sendReply(): void
    {
        $this->validate([
            'replyText' => 'required|string|max:3000',
        ]);

        if (! $this->selectedConversationId) {
            return;
        }

        $conversation = Conversation::findOrFail($this->selectedConversationId);
        $supportUser = User::getSupportUser();
        $senderId = Auth::id() ?? $supportUser->id;

        ConversationMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $senderId,
            'body' => trim($this->replyText),
            'read_at' => null,
        ]);

        $conversation->touch();
        $this->replyText = '';
    }

    protected function markConversationAsRead(int $conversationId): void
    {
        $supportUser = User::getSupportUser();

        // Mark all messages not sent by support staff as read
        ConversationMessage::where('conversation_id', $conversationId)
            ->whereNull('read_at')
            ->where('sender_id', '!=', $supportUser->id)
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        $supportUser = User::getSupportUser();

        $conversationsQuery = Conversation::query()
            ->whereIn('contextable_type', [User::class, 'user'])
            ->with(['contextable', 'latestMessage.sender'])
            ->withCount([
                'messages as unread_count' => function ($query) use ($supportUser) {
                    $query->whereNull('read_at')
                        ->where('sender_id', '!=', $supportUser->id);
                },
            ]);

        if (! empty($this->search)) {
            $term = '%' . $this->search . '%';
            $conversationsQuery->whereHasMorph('contextable', [User::class], function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('business_name', 'like', $term);
            });
        }

        $conversations = $conversationsQuery
            ->orderByDesc('updated_at')
            ->get();

        $activeConversation = null;
        $messages = collect();

        if ($this->selectedConversationId) {
            $activeConversation = Conversation::with(['contextable'])->find($this->selectedConversationId);
            if ($activeConversation) {
                $messages = ConversationMessage::with('sender')
                    ->where('conversation_id', $activeConversation->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        return view('livewire.admin.admin-support-conversations', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'messages' => $messages,
            'totalUnreadCount' => ConversationMessage::whereHas('conversation', function ($q) {
                $q->whereIn('contextable_type', [User::class, 'user']);
            })->whereNull('read_at')->where('sender_id', '!=', $supportUser->id)->count(),
        ]);
    }
}
