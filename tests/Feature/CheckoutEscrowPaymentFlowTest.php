<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\Invoices\InvoiceView;
use App\Livewire\Marketplace\CheckoutPage;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Country;
use App\Models\DeviceModel;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Payment;
use App\Models\User;
use App\Services\Commercial\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class CheckoutEscrowPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $seller;
    protected Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create([
            'name' => 'Buyer Joe',
            'email' => 'buyer_joe@example.com',
        ]);

        $this->seller = User::factory()->create([
            'name' => 'Tech Hub Seller',
            'email' => 'seller_tech@example.com',
        ]);

        $category = Category::firstOrCreate(['slug' => 'laptops-flow-test'], [
            'name' => 'Laptops Flow Test',
        ]);

        $model = DeviceModel::firstOrCreate(['slug' => 'dell-xps-13'], [
            'name' => 'Dell XPS 13',
            'category_id' => $category->id,
        ]);

        $item = Item::create([
            'user_id' => $this->seller->id,
            'model_id' => $model->id,
            'condition_status' => 'working',
        ]);

        $this->listing = Listing::create([
            'user_id' => $this->seller->id,
            'item_id' => $item->id,
            'title' => 'Dell XPS 13 Ultrabook',
            'price' => 100000.00,
            'quantity' => 2,
            'reserved_quantity' => 0,
            'sold_quantity' => 0,
            'condition' => 'used',
            'status' => 'active',
            'allow_shipping' => true,
        ]);
    }

    public function test_checkout_with_platform_escrow_redirects_to_payment_gateway_not_invoice(): void
    {
        $cartService = app(CartService::class);
        $cartService->addToCart($this->buyer, $this->listing, 1);

        $this->actingAs($this->buyer);

        $component = Livewire::test(CheckoutPage::class, ['seller' => $this->seller->id])
            ->set('deliveryMethod', 'pickup')
            ->set('paymentMethod', 'platform')
            ->call('placeOrder');

        // Verify payment record was created
        $payment = Payment::where('user_id', $this->buyer->id)->latest()->first();
        $this->assertNotNull($payment);
        $this->assertEquals('pending', $payment->status);
        $this->assertEquals('paystack', $payment->provider);

        // Verify invoice was created with issued status
        $invoice = Invoice::where('buyer_id', $this->buyer->id)->latest()->first();
        $this->assertNotNull($invoice);
        $this->assertEquals('issued', $invoice->status);
        $this->assertEquals('platform', $invoice->payment_method);

        // Listing quantity is reserved, not marked sold yet
        $this->listing->refresh();
        $this->assertEquals(1, $this->listing->reserved_quantity);
        $this->assertEquals(0, $this->listing->sold_quantity);

        // Buyer cart is cleared
        $this->assertDatabaseMissing('carts', [
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
        ]);

        // Assert user was redirected to payment gateway (NOT route('invoices') or route('invoices.view', ...))
        $expectedGatewayRedirect = route('payment.callback', [
            'reference' => $payment->reference,
            'provider' => 'paystack',
            'mock_success' => 1,
        ]);

        $component->assertRedirect($expectedGatewayRedirect);
    }

    public function test_checkout_with_direct_transfer_redirects_to_invoice(): void
    {
        $cartService = app(CartService::class);
        $cartService->addToCart($this->buyer, $this->listing, 1);

        $this->actingAs($this->buyer);

        $component = Livewire::test(CheckoutPage::class, ['seller' => $this->seller->id])
            ->set('deliveryMethod', 'pickup')
            ->set('paymentMethod', 'direct')
            ->call('placeOrder');

        // Invoice was created
        $invoice = Invoice::where('buyer_id', $this->buyer->id)->latest()->first();
        $this->assertNotNull($invoice);
        $this->assertEquals('issued', $invoice->status);
        $this->assertEquals('direct', $invoice->payment_method);

        // No payment table entry created for direct transfer
        $this->assertDatabaseMissing('payments', [
            'paymentable_id' => $invoice->id,
            'paymentable_type' => Invoice::class,
        ]);

        // Redirects directly to the specific invoice view
        $component->assertRedirect(route('invoices.view', $invoice->id));
    }

    public function test_payment_callback_verifies_payment_and_redirects_to_invoice_with_success(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-TESTSUCCESS',
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'subtotal' => 100000.00,
            'total' => 110000.00,
            'discount' => 0.00,
            'currency' => 'NGN',
            'status' => 'issued',
            'payment_method' => 'platform',
        ]);

        $payment = Payment::create([
            'user_id' => $this->buyer->id,
            'paymentable_id' => $invoice->id,
            'paymentable_type' => Invoice::class,
            'reference' => 'PAY-TESTSUCCESS123',
            'provider' => 'paystack',
            'status' => 'pending',
            'amount' => 110000.00,
            'currency' => 'NGN',
            'metadata' => [
                'invoice_id' => $invoice->id,
            ],
        ]);

        $response = $this->actingAs($this->buyer)->get(route('payment.callback', [
            'reference' => 'PAY-TESTSUCCESS123',
            'provider' => 'paystack',
            'mock_success' => 1,
        ]));

        // Assert redirected to the invoice
        $response->assertRedirect(route('invoices.view', $invoice->id));
        $response->assertSessionHas('success');

        // Verify database updates
        $payment->refresh();
        $this->assertEquals('successful', $payment->status);
        $this->assertNotNull($payment->paid_at);

        $invoice->refresh();
        $this->assertEquals('paid', $invoice->status);
        $this->assertNotNull($invoice->paid_at);

        // Verify invoice view displays success and paid status
        $viewResponse = $this->actingAs($this->buyer)->get(route('invoices.view', $invoice->id));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('PAID');
    }

    public function test_payment_callback_marks_failed_and_redirects_to_invoice_when_cancelled_or_failed(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-TESTFAIL',
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'subtotal' => 100000.00,
            'total' => 110000.00,
            'discount' => 0.00,
            'currency' => 'NGN',
            'status' => 'issued',
            'payment_method' => 'platform',
        ]);

        $payment = Payment::create([
            'user_id' => $this->buyer->id,
            'paymentable_id' => $invoice->id,
            'paymentable_type' => Invoice::class,
            'reference' => 'PAY-TESTFAIL123',
            'provider' => 'paystack',
            'status' => 'pending',
            'amount' => 110000.00,
            'currency' => 'NGN',
            'metadata' => [
                'invoice_id' => $invoice->id,
            ],
        ]);

        $response = $this->actingAs($this->buyer)->get(route('payment.callback', [
            'reference' => 'PAY-TESTFAIL123',
            'provider' => 'paystack',
            'mock_fail' => 1,
        ]));

        // Assert redirected to the invoice
        $response->assertRedirect(route('invoices.view', $invoice->id));
        $response->assertSessionHas('error');

        // Verify payment is failed and invoice is still unpaid/issued
        $payment->refresh();
        $this->assertEquals('failed', $payment->status);

        $invoice->refresh();
        $this->assertEquals('issued', $invoice->status);

        // Verify invoice view displays payment pending and error message
        $viewResponse = $this->actingAs($this->buyer)->get(route('invoices.view', $invoice->id));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('PAYMENT PENDING');
    }

    public function test_checkout_redirects_to_live_gateway_url_when_gateway_initialization_succeeds(): void
    {
        config(['services.payment.enable_gateway_http' => true]);
        config(['services.paystack.secret' => 'sk_test_mocksecret123']);

        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'message' => 'Authorization URL created',
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/test-auth-url-999',
                    'access_code' => 'test-auth-code',
                    'reference' => 'PAY-MOCKREF123',
                ],
            ], 200),
        ]);

        $cartService = app(CartService::class);
        $cartService->addToCart($this->buyer, $this->listing, 1);

        $this->actingAs($this->buyer);

        $component = Livewire::test(CheckoutPage::class, ['seller' => $this->seller->id])
            ->set('deliveryMethod', 'pickup')
            ->set('paymentMethod', 'platform')
            ->call('placeOrder');

        // Component redirects away to the Paystack checkout authorization url
        $component->assertRedirect('https://checkout.paystack.com/test-auth-url-999');
    }

    public function test_checkout_automatically_uses_country_first_payment_gateway_option(): void
    {
        $country = Country::create([
            'name' => 'Ghana Test',
            'code' => 'GH',
            'phone_code' => '+233',
            'currency' => 'GHS',
            'currency_symbol' => 'GH₵',
            'timezone' => 'UTC',
            'is_active' => true,
            'is_default' => false,
            'payment_gateway' => ['flutterwave', 'paystack'],
        ]);

        $this->seller->update(['country_id' => $country->id]);

        $cartService = app(CartService::class);
        $cartService->addToCart($this->buyer, $this->listing, 1);

        $this->actingAs($this->buyer);

        $component = Livewire::test(CheckoutPage::class, ['seller' => $this->seller->id])
            ->set('deliveryMethod', 'pickup')
            ->set('paymentMethod', 'platform')
            ->call('placeOrder');

        $payment = Payment::where('user_id', $this->buyer->id)->latest()->first();
        $this->assertNotNull($payment);
        // Automatically chose flutterwave as the country's first option
        $this->assertEquals('flutterwave', $payment->provider);

        $expectedRedirect = route('payment.callback', [
            'reference' => $payment->reference,
            'provider' => 'flutterwave',
            'mock_success' => 1,
        ]);
        $component->assertRedirect($expectedRedirect);
    }

    public function test_checkout_falls_back_to_second_gateway_when_primary_gateway_fails(): void
    {
        config(['services.payment.enable_gateway_http' => true]);
        config(['services.paystack.secret' => 'sk_paystack_test']);
        config(['services.flutterwave.secret' => 'flw_sec_key_test']);

        $country = Country::create([
            'name' => 'Kenya Test',
            'code' => 'KE',
            'phone_code' => '+254',
            'currency' => 'KES',
            'currency_symbol' => 'KSh',
            'timezone' => 'Africa/Nairobi',
            'is_active' => true,
            'is_default' => false,
            'payment_gateway' => ['paystack', 'flutterwave'],
        ]);

        $this->seller->update(['country_id' => $country->id]);

        // Paystack fails, Flutterwave succeeds
        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => false,
                'message' => 'Paystack service temporarily down',
            ], 500),
            'https://api.flutterwave.com/v3/payments' => Http::response([
                'status' => 'success',
                'message' => 'Hosted Link',
                'data' => [
                    'link' => 'https://checkout.flutterwave.com/pay/fallback-session-token',
                ],
            ], 200),
        ]);

        $cartService = app(CartService::class);
        $cartService->addToCart($this->buyer, $this->listing, 1);

        $this->actingAs($this->buyer);

        $component = Livewire::test(CheckoutPage::class, ['seller' => $this->seller->id])
            ->set('deliveryMethod', 'pickup')
            ->set('paymentMethod', 'platform')
            ->call('placeOrder');

        $payment = Payment::where('user_id', $this->buyer->id)->latest()->first();
        $this->assertNotNull($payment);
        // Provider was updated to fallback flutterwave
        $this->assertEquals('flutterwave', $payment->provider);

        // Redirects to the Flutterwave payment link
        $component->assertRedirect('https://checkout.flutterwave.com/pay/fallback-session-token');
    }

    public function test_invoice_view_falls_back_to_second_gateway_when_primary_gateway_fails(): void
    {
        config(['services.payment.enable_gateway_http' => true]);
        config(['services.paystack.secret' => 'sk_paystack_test']);
        config(['services.flutterwave.secret' => 'flw_sec_key_test']);

        $country = Country::create([
            'name' => 'Rwanda Test',
            'code' => 'RW',
            'phone_code' => '+250',
            'currency' => 'RWF',
            'currency_symbol' => 'FRw',
            'timezone' => 'Africa/Kigali',
            'is_active' => true,
            'is_default' => false,
            'payment_gateway' => ['paystack', 'flutterwave'],
        ]);

        $this->seller->update(['country_id' => $country->id]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-TESTFALLBACK',
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'subtotal' => 50000.00,
            'total' => 55000.00,
            'discount' => 0.00,
            'currency' => 'RWF',
            'status' => 'issued',
            'payment_method' => 'platform',
        ]);

        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => false,
                'message' => 'Paystack timeout',
            ], 504),
            'https://api.flutterwave.com/v3/payments' => Http::response([
                'status' => 'success',
                'message' => 'Hosted Link',
                'data' => [
                    'link' => 'https://checkout.flutterwave.com/pay/invoice-fallback-token',
                ],
            ], 200),
        ]);

        $this->actingAs($this->buyer);

        $component = Livewire::test(InvoiceView::class, ['identifier' => $invoice->id])
            ->call('payWithPlatformEscrow');

        $payment = Payment::where('user_id', $this->buyer->id)->where('paymentable_id', $invoice->id)->latest()->first();
        $this->assertNotNull($payment);
        $this->assertEquals('flutterwave', $payment->provider);

        $component->assertRedirect('https://checkout.flutterwave.com/pay/invoice-fallback-token');
    }
}
