<?php

namespace Tests\Feature;

use App\Livewire\Components\Messaging\ConversationDrawer;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ConversationDrawerSocketTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_message_succeeds_when_socket_id_header_is_undefined_string(): void
    {
        $sender = User::factory()->create([
            'name' => 'Alice Sender',
            'email' => 'alice_socket@example.com',
        ]);

        $recipient = User::factory()->create([
            'name' => 'Bob Recipient',
            'email' => 'bob_socket@example.com',
        ]);

        $conversation = Conversation::create([
            'created_by' => $sender->id,
            'title' => 'Test Socket Header Inquiry',
        ]);

        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $sender->id,
        ]);

        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $recipient->id,
        ]);

        $this->actingAs($sender);

        // Simulate Livewire request with X-Socket-ID: undefined header
        Livewire::withHeaders([
            'X-Socket-ID' => 'undefined',
        ])
        ->test(ConversationDrawer::class)
        ->call('loadConversation', $conversation->id)
        ->set('newMessage', 'Hello Bob, testing message with undefined socket ID!')
        ->call('sendMessage')
        ->assertDispatched('message-sent');

        $this->assertDatabaseHas('conversation_messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => 'Hello Bob, testing message with undefined socket ID!',
        ]);
    }

    public function test_send_message_succeeds_with_valid_socket_id(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $conversation = Conversation::create([
            'created_by' => $sender->id,
            'title' => 'Valid Socket Test',
        ]);

        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $sender->id,
        ]);
        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $recipient->id,
        ]);

        $this->actingAs($sender);

        Livewire::withHeaders([
            'X-Socket-ID' => '12345.67890',
        ])
        ->test(ConversationDrawer::class)
        ->call('loadConversation', $conversation->id)
        ->set('newMessage', 'Hello with valid socket ID!')
        ->call('sendMessage')
        ->assertDispatched('message-sent');

        $this->assertDatabaseHas('conversation_messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => 'Hello with valid socket ID!',
        ]);
    }
}
