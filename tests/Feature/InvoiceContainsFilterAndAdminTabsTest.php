<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminInvoices;
use App\Livewire\Admin\AdminInvoiceView;
use App\Livewire\Dashboard\Invoices\InvoicesList;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Issue;
use App\Models\Role;
use App\Models\ServiceJob;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvoiceContainsFilterAndAdminTabsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $buyer;
    protected User $seller;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create([
            'name' => 'Super Administrator',
            'slug' => 'super_admin',
            'description' => 'Platform admin',
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create(['role_id' => $role->id]);
        $this->buyer = User::factory()->create();
        $this->seller = User::factory()->create();
    }

    protected function createInvoice(string $number, array $attributes = []): Invoice
    {
        return Invoice::create(array_merge([
            'invoice_number' => $number,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'total' => 25000.00,
            'subtotal' => 25000.00,
            'currency' => 'NGN',
            'status' => 'paid',
            'payment_method' => 'platform',
            'escrow_status' => 'held',
            'due_at' => now()->addDays(3),
        ], $attributes));
    }

    protected function createShipment(Invoice $invoice, array $attributes = []): Shipment
    {
        $shipment = Shipment::create(array_merge([
            'sender_id' => $invoice->seller_id,
            'receiver_id' => $invoice->buyer_id,
            'provider_name' => 'DHL Express',
            'tracking_number' => 'DHL-' . uniqid(),
            'status' => 'pending',
            'origin_address_line_1' => 'Seller Dispatch Hub',
            'origin_city' => 'Lagos',
            'destination_address_line_1' => 'Buyer Destination Address',
            'destination_city' => 'Lagos',
        ], $attributes));

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'itemable_type' => Shipment::class,
            'itemable_id' => $shipment->id,
            'type' => 'delivery',
            'description' => 'Express Courier Delivery',
            'quantity' => 1,
            'unit_price' => 5000,
            'amount' => 5000,
        ]);

        return $shipment;
    }

    public function test_admin_invoices_contains_filter(): void
    {
        $invoiceWithShipment = $this->createInvoice('INV-TEST-SHIP-001');
        $this->createShipment($invoiceWithShipment, ['provider_name' => 'DHL', 'tracking_number' => 'DHL-998877']);

        $invoiceWithIssue = $this->createInvoice('INV-TEST-ISSUE-002', [
            'escrow_status' => 'disputed',
        ]);

        Issue::create([
            'invoice_id' => $invoiceWithIssue->id,
            'reported_by' => $this->buyer->id,
            'type' => 'damaged',
            'description' => 'Item arrived damaged in transit',
            'status' => 'open',
        ]);

        // Test filtering by shipment
        Livewire::actingAs($this->admin)
            ->test(AdminInvoices::class)
            ->set('contains', 'shipment')
            ->assertSee('INV-TEST-SHIP-001')
            ->assertDontSee('INV-TEST-ISSUE-002');

        // Test filtering by issue
        Livewire::actingAs($this->admin)
            ->test(AdminInvoices::class)
            ->set('contains', 'issue')
            ->assertSee('INV-TEST-ISSUE-002')
            ->assertDontSee('INV-TEST-SHIP-001');
    }

    public function test_user_invoices_list_contains_filter(): void
    {
        $invoiceWithShipment = $this->createInvoice('INV-USER-SHIP-001');
        $this->createShipment($invoiceWithShipment, ['provider_name' => 'DHL', 'tracking_number' => 'DHL-USER-123']);

        $invoiceOther = $this->createInvoice('INV-USER-OTHER-002');

        Livewire::actingAs($this->buyer)
            ->test(InvoicesList::class)
            ->set('contains', 'shipment')
            ->assertSee('INV-USER-SHIP-001')
            ->assertDontSee('INV-USER-OTHER-002');
    }

    public function test_admin_invoice_view_renders_tabs_and_read_only_statuses(): void
    {
        $invoice = $this->createInvoice('INV-ADMIN-TABS-001');
        $this->createShipment($invoice, ['provider_name' => 'SpeedPost', 'tracking_number' => 'SPD-887766']);

        Issue::create([
            'invoice_id' => $invoice->id,
            'reported_by' => $this->buyer->id,
            'type' => 'damaged',
            'description' => 'Cracked screen reported by buyer',
            'status' => 'open',
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(AdminInvoiceView::class, ['invoice' => $invoice]);

        // Default details tab
        $component->assertSee('INV-ADMIN-TABS-001')
            ->assertSee('Invoice Items & Services')
            ->assertSee('Shipment')
            ->assertSee('Issues');

        // Switch to shipment tab
        $component->set('activeTab', 'shipment')
            ->assertSet('activeTab', 'shipment')
            ->assertSee('Waiting for Vendor / Seller to dispatch')
            ->assertSee('SpeedPost');

        // Switch to issues tab
        $component->set('activeTab', 'issue')
            ->assertSet('activeTab', 'issue')
            ->assertSee('Waiting for Seller Response')
            ->assertSee('Cracked screen reported by buyer');
    }
}
