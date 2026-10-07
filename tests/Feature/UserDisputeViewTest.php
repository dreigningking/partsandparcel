<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\Disputes\DisputeView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserDisputeViewTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'is_verified' => true,
        ]);
    }

    public function test_user_can_view_static_dispute_view_at_disputes_1(): void
    {
        $this->actingAs($this->user)
            ->get('/disputes/1')
            ->assertStatus(200)
            ->assertSee('Dispute Case #DSP-0001')
            ->assertSee('TechSam Autos')
            ->assertSee('Abel Auto Parts &amp; Diagnostics', false)
            ->assertSee('Frozen Escrow: ₦145,000.00')
            ->assertSee('Evidence Locker &amp; Requests', false)
            ->assertDontSee('Buyer-Seller Conversation')
            ->assertDontSee('Admin Arbitration Panel')
            ->assertDontSee('Execute Binding Arbitration Decision');
    }

    public function test_dispute_view_default_tab_is_evidence_and_tabs_can_switch(): void
    {
        Livewire::actingAs($this->user)
            ->test(DisputeView::class, ['dispute_id' => 1])
            ->assertSet('activeTab', 'evidence')
            ->assertSee('Evidence Verification &amp; Party Requisition Locker', false)
            ->assertDontSee('Buyer-Seller Conversation')
            ->call('setTab', 'claims')
            ->assertSet('activeTab', 'claims')
            ->assertSee('Invoice Disputed Item Breakdown')
            ->call('setTab', 'logistics')
            ->assertSet('activeTab', 'logistics')
            ->assertSee('Outbound Courier Shipment')
            ->call('setTab', 'timeline')
            ->assertSet('activeTab', 'timeline')
            ->assertSee('Audit Log &amp; Lifecycle', false);
    }

    public function test_dispute_view_can_switch_perspective(): void
    {
        Livewire::actingAs($this->user)
            ->test(DisputeView::class, ['dispute_id' => 1])
            ->assertSet('viewAs', 'buyer')
            ->call('setPerspective', 'seller')
            ->assertSet('viewAs', 'seller');
    }

    public function test_party_can_submit_requested_evidence(): void
    {
        Livewire::actingAs($this->user)
            ->test(DisputeView::class, ['dispute_id' => 1])
            ->call('openUploadModal', 103)
            ->assertSet('showUploadModal', true)
            ->assertSet('activeRequestId', 103)
            ->set('uploadNotes', 'Attached high-res photo of wiring harness plug with zero burn marks.')
            ->call('submitEvidence')
            ->assertSet('showUploadModal', false)
            ->assertSet('activeRequestId', null)
            ->assertSee('Your evidence documents have been successfully submitted to the platform arbitrator.');
    }

    public function test_dispute_view_can_toggle_resolution_status(): void
    {
        Livewire::actingAs($this->user)
            ->test(DisputeView::class, ['dispute_id' => 1])
            ->assertSet('status', 'open')
            ->assertSee('Arbitration Review in Progress — Escrow Secured')
            ->call('toggleResolutionStatus')
            ->assertSet('status', 'resolved')
            ->assertSee('Official Mediator Binding Verdict &amp; Financial Settlement', false)
            ->assertSee('Ruled in Buyer\'s Favor', false)
            ->assertDontSee('Execute Binding Arbitration Decision');
    }

    public function test_user_view_loads_live_dispute_and_submits_evidence_to_database(): void
    {
        $seller = User::factory()->create(['name' => 'Abel Electronics', 'email' => 'abel@electronics.ng']);

        $invoice = \App\Models\Invoice::create([
            'invoice_number' => 'INV-USER-DISP-1',
            'buyer_id' => $this->user->id,
            'seller_id' => $seller->id,
            'subtotal' => 110000,
            'total' => 110000,
            'status' => 'paid',
            'paid_at' => now()->subDays(2),
        ]);

        $issue = \App\Models\Issue::create([
            'invoice_id' => $invoice->id,
            'reported_by' => $this->user->id,
            'type' => 'wrong_item',
            'status' => 'open',
            'description' => 'Received wrong alternators.',
        ]);

        $dispute = \App\Models\Dispute::create([
            'invoice_id' => $invoice->id,
            'issue_id' => $issue->id,
            'opened_by' => $this->user->id,
            'respondent_id' => $seller->id,
            'type' => 'item_mismatch',
            'status' => 'open',
            'reason' => 'Delivered 12V alternators instead of 24V commercial alternator specs.',
        ]);

        $evidence = \App\Models\DisputeEvidence::create([
            'dispute_id' => $dispute->id,
            'requested_by' => null,
            'target_party' => 'buyer',
            'target_user_id' => $this->user->id,
            'title' => 'Part rating plate close-up',
            'instructions' => 'Photograph the metal rating plate showing voltage stamping.',
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->user)
            ->test(DisputeView::class, ['dispute_id' => $dispute->id])
            ->assertSet('disputeId', $dispute->id)
            ->assertSet('viewAs', 'buyer')
            ->assertSee('Part rating plate close-up')
            ->assertSee('INV-USER-DISP-1')
            ->call('openUploadModal', $evidence->id)
            ->assertSet('showUploadModal', true)
            ->assertSet('activeRequestId', $evidence->id)
            ->set('uploadNotes', 'Here is the stamped rating plate clearly indicating 12V 90A.')
            ->call('submitEvidence')
            ->assertSet('showUploadModal', false);

        $this->assertDatabaseHas('dispute_evidence', [
            'id' => $evidence->id,
            'status' => 'submitted',
            'party_notes' => 'Here is the stamped rating plate clearly indicating 12V 90A.',
            'submitted_by' => $this->user->id,
        ]);
    }
}
