<?php

namespace App\Livewire\Dashboard\Messages;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dash')]
class MessageList extends Component{

    public ?string $activeConversationId = null;
    public string $searchQuery = '';
    public string $activeTab = 'all'; // 'all', 'unread', 'offers', 'orders'

    public function selectConversation($id)
    {
        $this->activeConversationId = $id;
    }

    public function clearConversation()
    {
        $this->activeConversationId = null;
    }

    public function render()
    {
        return view('livewire.dashboard.messages.message-list');
    }
}
