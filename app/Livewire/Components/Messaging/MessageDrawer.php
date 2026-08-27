<?php

namespace App\Livewire\Components\Messaging;

use Livewire\Attributes\On;
use Livewire\Component;

class MessageDrawer extends Component
{
    public $isOpen = false;

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
        $this->dispatch('open-conversation', id: $id);
    }

    public function render()
    {
        return view('livewire.components.messaging.message-drawer');
    }
}