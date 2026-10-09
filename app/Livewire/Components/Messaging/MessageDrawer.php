<?php

namespace App\Livewire\Components\Messaging;

use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class MessageDrawer extends Component
{
    public bool $isOpen = false;
    public string $search = '';

    public function getListeners()
    {
        $userId = Auth::id();
        $listeners = [
            'open-message-drawer' => 'openDrawer',
            'close-message-drawer' => 'closeDrawer',
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
        // Re-renders the component: conversation list re-queries with updated_at desc
    }

    #[On('open-message-drawer')]
    public function openDrawer()
    {
        $this->isOpen = true;
    }

    #[On('close-message-drawer')]
    public function closeDrawer()
    {
        $this->isOpen = false;
    }

    public function openConversation($id)
    {
        $this->isOpen = false;
        $this->dispatch('open-conversation', id: $id);
    }

    public function render()
    {
        $user = Auth::user();
        $conversations = collect();

        if ($user) {
            $query = Conversation::where(function ($q) use ($user) {
                $q->where(function ($sub) use ($user) {
                    $sub->support()
                        ->where(function ($sq) use ($user) {
                            $sq->where('contextable_id', $user->id)
                               ->orWhereHas('participants', fn($p) => $p->where('user_id', $user->id));
                        });
                })->orWhereHas('participants', function ($p) use ($user) {
                    $p->where('user_id', $user->id);
                });
            })
            ->with(['participants.user', 'latestMessage.sender', 'contextable'])
            ->latest('updated_at');

            if (trim($this->search) !== '') {
                $searchTerm = '%' . trim($this->search) . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereHas('participants.user', function ($uq) use ($searchTerm) {
                        $uq->where('name', 'like', $searchTerm)
                           ->orWhere('business_name', 'like', $searchTerm);
                    })->orWhereHas('messages', function ($mq) use ($searchTerm) {
                        $mq->where('body', 'like', $searchTerm);
                    });
                });
            }

            $conversations = $query->take(20)->get()->map(function ($c) use ($user) {
                $peerName = $c->getOtherPartyName($user);
                $latest = $c->latestMessage;
                $unread = $c->messages()->where('sender_id', '!=', $user->id)->whereNull('read_at')->exists();

                return [
                    'id' => $c->id,
                    'peer_name' => $peerName,
                    'avatar' => $c->isSupport() ? '🎧' : strtoupper(substr($peerName, 0, 1)),
                    'title' => $c->contextable?->title ?: ($c->isSupport() ? 'Official Support Desk' : 'Direct Inquiry'),
                    'snippet' => $latest?->body ?: 'No messages yet',
                    'time' => $latest?->created_at ? $latest->created_at->diffForHumans() : $c->created_at->diffForHumans(),
                    'unread' => $unread,
                ];
            });
        }

        $unreadCount = $conversations->where('unread', true)->count();

        return view('livewire.components.messaging.message-drawer', [
            'conversations' => $conversations,
            'unreadCount' => $unreadCount,
        ]);
    }
}