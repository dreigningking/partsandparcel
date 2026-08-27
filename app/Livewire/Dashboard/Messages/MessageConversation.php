<?php

namespace App\Livewire\Dashboard\Messages;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class MessageConversation extends Component
{
    public $conversationId = 'adam';

    public function mount()
    {
        $this->conversationId = request()->query('conv', 'adam');
    }

    public function render()
    {
        return view('livewire.dashboard.messages.message-conversation');
    }
}