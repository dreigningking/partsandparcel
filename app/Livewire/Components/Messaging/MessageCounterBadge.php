<?php

namespace App\Livewire\Components\Messaging;

use App\Models\ConversationMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class MessageCounterBadge extends Component
{
    public int $unreadCount = 0;

    public function mount()
    {
        $this->updateCount();
    }

    public function getListeners()
    {
        $userId = Auth::id();
        $listeners = [
            'messages-marked-read' => 'updateCount',
            'message-sent' => 'updateCount',
        ];

        if ($userId) {
            $listeners["echo-private:user.{$userId},MessageSent"] = 'incrementFromBroadcast';
        }

        return $listeners;
    }

    public function updateCount()
    {
        $user = Auth::user();
        if (! $user) {
            $this->unreadCount = 0;
            return;
        }

        $this->unreadCount = ConversationMessage::whereHas('conversation.participants', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->where('sender_id', '!=', $user->id)
        ->whereNull('read_at')
        ->count();
    }

    public function incrementFromBroadcast($payload = null)
    {
        $this->unreadCount++;
    }

    public function openDrawer()
    {
        $this->dispatch('open-message-drawer');
    }

    public function render()
    {
        return view('livewire.components.messaging.message-counter-badge');
    }
}
