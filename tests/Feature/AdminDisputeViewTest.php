<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDisputeView;
use App\Models\Dispute;
use App\Models\DisputeEvidence;
use App\Models\Invoice;
use App\Models\Issue;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminDisputeViewTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create([
            'name' => 'Super Administrator',
            'slug' => 'super-admin',
            'permissions' => ['*'],
        ]);
        $this->admin = User::factory()->create([
            'role_id' => $role->id,
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);
    }

    public function test_admin_can_view_dispute_with_parties_first_and_no_chat_tab(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/disputes/1')
            ->assertStatus(200)
            ->assertSee('Dispute Case #DSP-0001')
            ->assertSee('TechSam Autos')
            ->assertSee('Abel Auto Parts')
            ->assertSee('Evidence Locker &amp; Requests', false)
            ->assertDontSee('Buyer-Seller Conversation')
            ->assertSee('Frozen Escrow: ₦145,000.00')
            ->assertSee('Official Arbitration Determination Bench');
    }

    public function test_admin_can_switch_full_width_tabs(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminDisputeView::class, ['id' => 1])
            ->assertSet('activeTab', 'evidence')
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

    public function test_admin_can_request_evidence_from_party(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminDisputeView::class, ['id' => 1])
            ->call('openRequestModal', 'seller')
            ->assertSet('showRequestModal', true)
            ->assertSet('requestTarget', 'seller')
            ->set('requestTitle', 'Packaging tape macro photo')
            ->set('requestInstructions', 'Please upload a photo showing the yellow seal serial number clearly.')
            ->call('submitEvidenceRequest')
            ->assertSet('showRequestModal', false)
            ->assertSee('Packaging tape macro photo')
            ->assertSee('Awaiting Submission from Abel Auto Parts');
    }

    public function test_admin_can_simulate_party_submission_for_pending_evidence(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminDisputeView::class, ['id' => 1])
            ->call('simulatePartySubmission', 103)
            ->assertSee('Simulated party submission received')
            ->assertSee('Verified Submission Document');
    }

    public function test_admin_can_execute_arbitration_ruling_on_bottom_bench(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminDisputeView::class, ['id' => 1])
            ->call('setDecision', 'buyer_favor')
            ->assertSet('decision', 'buyer_favor')
            ->call('executeArbitration')
            ->assertSet('isResolved', true)
            ->assertSee('Ruling Registered &amp; Settled', false);
    }

    public function test_admin_view_loads_live_dispute_and_lifecycle_timeline_from_database(): void
    {
        $buyer = User::factory()->create(['name' => 'AutoPoint Lagos', 'email' => 'autopoint@lagos.ng']);
        $seller = User::factory()->create(['name' => 'Kano Parts Ltd', 'email' => 'kano@parts.ng']);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-999',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'subtotal' => 80000,
            'total' => 80000,
            'status' => 'paid',
            'paid_at' => now()->subDays(3),
        ]);

        $issue = Issue::create([
            'invoice_id' => $invoice->id,
            'reported_by' => $buyer->id,
            'type' => 'damaged',
            'status' => 'open',
            'description' => 'Cracked manifold casing on arrival.',
        ]);

        $dispute = Dispute::create([
            'invoice_id' => $invoice->id,
            'issue_id' => $issue->id,
            'opened_by' => $buyer->id,
            'respondent_id' => $seller->id,
            'type' => 'rejection_contested',
            'status' => 'open',
            'reason' => 'Buyer rejected manifold package, seller claims package was intact.',
        ]);

        DisputeEvidence::create([
            'dispute_id' => $dispute->id,
            'requested_by' => $this->admin->id,
            'target_party' => 'seller',
            'target_user_id' => $seller->id,
            'title' => 'Courier handover dispatch manifest',
            'instructions' => 'Provide stamped manifest from courier.',
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AdminDisputeView::class, ['id' => $dispute->id])
            ->assertSet('disputeId', $dispute->id)
            ->assertSee('AutoPoint Lagos')
            ->assertSee('Kano Parts Ltd')
            ->assertSee('INV-TEST-999')
            ->assertSee('Courier handover dispatch manifest')
            ->assertDontSee('Buyer-Seller Conversation');
    }
}
