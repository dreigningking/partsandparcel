<?php

namespace App\Livewire\Components\Messaging;

use Livewire\Attributes\On;
use Livewire\Component;

class ConversationDrawer extends Component
{
    public $conversationId = null;
    public $isOpen = false;
    public $recipientName = 'Adam Computers';
    public $itemTitle = 'HP EliteBook 840 G5';
    public $avatarLetter = 'A';
    public $messages = [];

    #[On('open-conversation')]
    public function loadConversation($id)
    {
        $this->conversationId = $id;
        $this->isOpen = true;

        if ($id === 'abel') {
            $this->recipientName = 'Abel Electronics Hub';
            $this->itemTitle = 'Dell Latitude 5420 Motherboard';
            $this->avatarLetter = 'A';
            $this->messages = [
                ['sender' => 'them', 'text' => 'Can you confirm if your model uses the i5 11th Gen processor?', 'time' => '24m ago'],
                ['sender' => 'me', 'text' => 'Yes, it is the Core i5 11th Gen board.', 'time' => '20m ago']
            ];
        } elseif ($id === 'seth') {
            $this->recipientName = 'Seth Repair Yard';
            $this->itemTitle = 'HP EliteBook 840 Battery';
            $this->avatarLetter = 'S';
            $this->messages = [
                ['sender' => 'them', 'text' => 'Yes, I have 3 tested battery units available for pickup in Ikeja.', 'time' => '1h ago']
            ];
        } else {
            $this->recipientName = 'Adam Computers';
            $this->itemTitle = 'HP EliteBook 840 G5';
            $this->avatarLetter = 'A';
            $this->messages = [
                ['sender' => 'them', 'text' => 'I can accept ₦480,000 if you can arrange pickup.', 'time' => '2:14 PM'],
                ['sender' => 'me', 'text' => 'Can I test the laptop before payment?', 'time' => '2:17 PM'],
                ['sender' => 'them', 'text' => 'Yes. You can test it at my location before completing purchase.', 'time' => '2:19 PM']
            ];
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