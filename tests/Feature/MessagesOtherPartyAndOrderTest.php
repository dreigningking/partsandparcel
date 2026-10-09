<?php

namespace Tests\Feature;

use App\Livewire\Components\Messaging\ConversationDrawer;
use App\Livewire\Dashboard\Messages\MessageConversation;
use App\Livewire\Dashboard\Messages\MessageList;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\ConversationParticipant;
use App\Models\Discussion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MessagesOtherPartyAndOrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $vendor;
    protected Conversation $conversation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create([
            'name' => 'John Buyer',
            'email' => 'john_buyer@example.com',
        ]);

        $this->vendor = User::factory()->create([
            'name' => 'Acme Vendor',
            'business_name' => 'Acme Spare Parts Hub',
            'email' => 'acme_vendor@example.com',
        ]);

        $this->conversation = Conversation::create([
            'created_by' => $this->buyer->id,
            'title' => 'Inquiry on Dell Motherboard',
        ]);

        ConversationParticipant::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->buyer->id,
        ]);

        ConversationParticipant::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->vendor->id,
        ]);

        // Create messages with deliberate distinct timestamps
        ConversationMessage::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->buyer->id,
            'body' => 'First message: Is the board available?',
            'created_at' => now()->subMinutes(10),
        ]);

        ConversationMessage::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->vendor->id,
            'body' => 'Second message: Yes, 2 units in stock.',
            'created_at' => now()->subMinutes(5),
        ]);

        ConversationMessage::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->buyer->id,
            'body' => 'Third message: Can you do pickup today?',
            'created_at' => now()->subMinutes(1),
        ]);
    }

    public function test_message_list_shows_other_party_name_and_not_conversation_number(): void
    {
        $this->actingAs($this->buyer);

        Livewire::test(MessageList::class)
            ->assertSee('Acme Spare Parts Hub')
            ->assertDontSee('Conversation #' . $this->conversation->id);
    }

    public function test_message_list_messages_are_arranged_in_entry_order(): void
    {
        $this->actingAs($this->buyer);

        $component = Livewire::test(MessageList::class)
            ->call('selectConversation', $this->conversation->id);

        $html = $component->html();
        $streamHtml = substr($html, strpos($html, 'CHAT MESSAGES STREAM') ?: 0);

        $pos1 = strpos($streamHtml, 'First message: Is the board available?');
        $pos2 = strpos($streamHtml, 'Second message: Yes, 2 units in stock.');
        $pos3 = strpos($streamHtml, 'Third message: Can you do pickup today?');

        $this->assertNotFalse($pos1);
        $this->assertNotFalse($pos2);
        $this->assertNotFalse($pos3);

        $this->assertTrue($pos1 < $pos2, 'First message must appear before second message');
        $this->assertTrue($pos2 < $pos3, 'Second message must appear before third message');
    }

    public function test_conversation_drawer_shows_other_party_and_messages_in_entry_order(): void
    {
        $this->actingAs($this->buyer);

        $component = Livewire::test(ConversationDrawer::class)
            ->call('loadConversation', $this->conversation->id)
            ->assertSet('recipientName', 'Acme Spare Parts Hub');

        $messages = $component->get('messages');
        $this->assertCount(3, $messages);
        $this->assertEquals('First message: Is the board available?', $messages[0]['text']);
        $this->assertEquals('Second message: Yes, 2 units in stock.', $messages[1]['text']);
        $this->assertEquals('Third message: Can you do pickup today?', $messages[2]['text']);
    }

    public function test_message_conversation_shows_other_party_and_messages_in_entry_order(): void
    {
        $this->actingAs($this->buyer);

        $component = Livewire::withQueryParams(['conv' => $this->conversation->id])
            ->test(MessageConversation::class)
            ->assertSet('otherPartyName', 'Acme Spare Parts Hub');

        $messages = $component->get('messages');
        $this->assertCount(3, $messages);
        $this->assertEquals('First message: Is the board available?', $messages[0]['body']);
        $this->assertEquals('Second message: Yes, 2 units in stock.', $messages[1]['body']);
        $this->assertEquals('Third message: Can you do pickup today?', $messages[2]['body']);
    }

    public function test_message_list_does_not_auto_select_on_mount(): void
    {
        $this->actingAs($this->buyer);

        Livewire::test(MessageList::class)
            ->assertSet('activeConversationId', null)
            ->assertSee('No Conversation Selected')
            ->assertSee('Select a conversation from the list to view and reply to messages.');
    }

    public function test_message_list_selects_conversation_when_conv_query_param_provided(): void
    {
        $this->actingAs($this->buyer);

        Livewire::withQueryParams(['conv' => $this->conversation->id])
            ->test(MessageList::class)
            ->assertSet('activeConversationId', $this->conversation->id)
            ->assertDontSee('No Conversation Selected')
            ->assertSee('Acme Spare Parts Hub');
    }

    public function test_message_list_moves_sender_to_top_with_unread_badge_on_realtime_message(): void
    {
        $this->actingAs($this->buyer);

        \App\Models\Conversation::query()->update(['updated_at' => now()->subHours(2)]);

        // Create second vendor and newer conversation
        $vendor2 = User::factory()->create([
            'name' => 'Second Vendor',
            'business_name' => 'Second Shop',
            'email' => 'second_vendor@example.com',
        ]);
        $newerConv = Conversation::create([
            'created_by' => $this->buyer->id,
            'title' => 'Inquiry for Screen',
        ]);
        ConversationParticipant::create(['conversation_id' => $newerConv->id, 'user_id' => $this->buyer->id]);
        ConversationParticipant::create(['conversation_id' => $newerConv->id, 'user_id' => $vendor2->id]);
        ConversationMessage::create([
            'conversation_id' => $newerConv->id,
            'sender_id' => $vendor2->id,
            'body' => 'Recent screen update',
        ]);
        // Explicitly set older updated_at for this->conversation and newer for newerConv
        \DB::table('conversations')->where('id', $this->conversation->id)->update(['updated_at' => now()->subMinutes(30)]);
        \DB::table('conversations')->where('id', $newerConv->id)->update(['updated_at' => now()->subMinutes(5)]);

        // When MessageList is initially rendered, $newerConv is at the top
        $test = Livewire::test(MessageList::class);
        $conversations = $test->viewData('conversations');
        $this->assertEquals($newerConv->id, $conversations->first()->id);

        // Vendor 1 sends a new message to Buyer in $this->conversation
        $newMsg = ConversationMessage::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->vendor->id,
            'body' => 'Realtime urgent reply from Acme Vendor',
            'created_at' => now()->addMinutes(1),
        ]);

        // Trigger onMessageReceived payload on Buyer's MessageList
        $test->call('onMessageReceived', [
            'id' => $newMsg->id,
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->vendor->id,
            'sender_name' => 'Acme Spare Parts Hub',
            'body' => 'Realtime urgent reply from Acme Vendor',
        ]);

        // Now $this->conversation must have jumped to the top
        $updatedConversations = $test->viewData('conversations');
        $this->assertEquals($this->conversation->id, $updatedConversations->first()->id);
        $this->assertGreaterThan(0, $updatedConversations->first()->unread_count);
        $test->assertSee('Realtime urgent reply from Acme Vendor');
    }

    public function test_message_conversation_appends_incoming_message_in_realtime(): void
    {
        $this->actingAs($this->buyer);

        $test = Livewire::withQueryParams(['conv' => $this->conversation->id])
            ->test(MessageConversation::class);

        $this->assertCount(3, $test->get('messages'));

        // Realtime message arrives from vendor
        $newMsg = ConversationMessage::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->vendor->id,
            'body' => 'Are you still coming for pickup?',
            'created_at' => now(),
        ]);

        $test->call('onMessageReceived', [
            'id' => $newMsg->id,
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->vendor->id,
            'sender_name' => 'Acme Spare Parts Hub',
            'body' => 'Are you still coming for pickup?',
        ]);

        $messages = $test->get('messages');
        $this->assertCount(4, $messages);
        $this->assertEquals('Are you still coming for pickup?', end($messages)['body']);
        $test->assertSee('Are you still coming for pickup?');
    }

    public function test_conversation_drawer_appends_incoming_message_in_realtime(): void
    {
        $this->actingAs($this->buyer);

        $test = Livewire::test(ConversationDrawer::class)
            ->call('loadConversation', $this->conversation->id);

        $this->assertCount(3, $test->get('messages'));

        $newMsg = ConversationMessage::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->vendor->id,
            'body' => 'Drawer realtime notification message',
            'created_at' => now(),
        ]);

        $test->call('onMessageReceived', [
            'id' => $newMsg->id,
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->vendor->id,
            'sender_name' => 'Acme Spare Parts Hub',
            'body' => 'Drawer realtime notification message',
        ]);

        $messages = $test->get('messages');
        $this->assertCount(4, $messages);
        $this->assertEquals('Drawer realtime notification message', end($messages)['text']);
        $test->assertSee('Drawer realtime notification message');
    }
}

