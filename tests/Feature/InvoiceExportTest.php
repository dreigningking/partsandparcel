<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_export_invoices_to_excel(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        Invoice::create([
            'invoice_number' => 'INV-TEST-001',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'subtotal' => 50000,
            'tax' => 2500,
            'commission' => 2500,
            'total' => 52500,
            'status' => 'paid',
            'payment_method' => 'platform',
        ]);

        $response = $this->actingAs($buyer)->get('/invoices/export/excel');
        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-disposition'), 'invoices-')
        );
    }

    public function test_authorized_user_can_download_invoice_pdf(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-002',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'subtotal' => 75000,
            'tax' => 0,
            'commission' => 3750,
            'total' => 75000,
            'status' => 'paid',
            'payment_method' => 'platform',
        ]);

        // Buyer can download
        $resBuyer = $this->actingAs($buyer)->get("/invoices/{$invoice->id}/export/pdf");
        $resBuyer->assertStatus(200);
        $this->assertEquals('application/pdf', $resBuyer->headers->get('content-type'));

        // Seller can download
        $resSeller = $this->actingAs($seller)->get("/invoices/{$invoice->id}/export/pdf");
        $resSeller->assertStatus(200);
        $this->assertEquals('application/pdf', $resSeller->headers->get('content-type'));
    }

    public function test_unauthorized_user_cannot_download_other_users_invoice_pdf(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $otherUser = User::factory()->create(['role_id' => null]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-003',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'subtotal' => 20000,
            'tax' => 0,
            'commission' => 1000,
            'total' => 20000,
            'status' => 'issued',
            'payment_method' => 'platform',
        ]);

        $response = $this->actingAs($otherUser)->get("/invoices/{$invoice->id}/export/pdf");
        $response->assertStatus(403);
    }
}
