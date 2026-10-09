<?php

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Livewire\Dashboard\Disputes\DisputeView;
use App\Livewire\Dashboard\Invoices\InvoiceView;
use App\Livewire\Dashboard\Offers\OfferView;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Dispute;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Issue;
use App\Models\Item;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityAuthorizationHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $seller;
    protected User $stranger;
    protected User $admin;
    protected Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create([
            'name' => 'Legit Buyer',
            'email' => 'buyer_auth@example.com',
            'is_verified' => true,
        ]);

        $this->seller = User::factory()->create([
            'name' => 'Legit Seller',
            'email' => 'seller_auth@example.com',
            'is_verified' => true,
        ]);

        $this->stranger = User::factory()->create([
            'name' => 'Malicious Stranger',
            'email' => 'stranger_auth@example.com',
            'is_verified' => true,
        ]);

        $adminRole = \App\Models\Role::firstOrCreate(['slug' => 'super_admin'], [
            'name' => 'Super Admin',
            'permissions' => ['*'],
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Platform Admin',
            'email' => 'admin_auth@example.com',
            'role_id' => $adminRole->id,
            'is_verified' => true,
        ]);

        $this->invoice = Invoice::create([
            'invoice_number' => 'INV-SEC-TEST-001',
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'delivery_method' => 'buyer_responsible',
            'subtotal' => 50000.00,
            'total' => 50000.00,
            'currency' => 'NGN',
            'payment_method' => 'platform',
            'status' => 'paid',
            'paid_at' => now(),
            'issued_at' => now(),
        ]);
    }

    public function test_invoice_view_permits_buyer_and_seller(): void
    {
        // Buyer access
        Livewire::actingAs($this->buyer)
            ->test(InvoiceView::class, ['invoice_id' => $this->invoice->id])
            ->assertStatus(200)
            ->assertSet('invoice.id', $this->invoice->id);

        // Seller access
        Livewire::actingAs($this->seller)
            ->test(InvoiceView::class, ['invoice_id' => $this->invoice->id])
            ->assertStatus(200)
            ->assertSet('invoice.id', $this->invoice->id);
    }

    public function test_invoice_view_blocks_unauthorized_stranger_with_403(): void
    {
        Livewire::actingAs($this->stranger)
            ->test(InvoiceView::class, ['invoice_id' => $this->invoice->id])
            ->assertStatus(403);
    }

    public function test_invoice_view_permits_admin(): void
    {
        Livewire::actingAs($this->admin)
            ->test(InvoiceView::class, ['invoice_id' => $this->invoice->id])
            ->assertStatus(200)
            ->assertSet('invoice.id', $this->invoice->id);
    }

    public function test_invoice_view_aborts_404_for_non_existent_invoice(): void
    {
        Livewire::actingAs($this->buyer)
            ->test(InvoiceView::class, ['invoice_id' => 999999])
            ->assertStatus(404);
    }

    public function test_dispute_view_permits_involved_parties_and_blocks_stranger(): void
    {
        $issue = Issue::create([
            'invoice_id' => $this->invoice->id,
            'reported_by' => $this->buyer->id,
            'type' => 'damaged',
            'description' => 'Damaged on delivery',
            'status' => 'open',
        ]);

        $dispute = Dispute::create([
            'invoice_id' => $this->invoice->id,
            'opened_by' => $this->buyer->id,
            'respondent_id' => $this->seller->id,
            'issue_id' => $issue->id,
            'type' => 'rejection_contested',
            'reason' => 'Package arrived completely crushed.',
            'status' => 'open',
            'decision' => 'buyer_favor',
        ]);

        // Buyer access permitted
        Livewire::actingAs($this->buyer)
            ->test(DisputeView::class, ['dispute_id' => $dispute->id])
            ->assertStatus(200);

        // Seller access permitted
        Livewire::actingAs($this->seller)
            ->test(DisputeView::class, ['dispute_id' => $dispute->id])
            ->assertStatus(200);

        // Admin access permitted
        Livewire::actingAs($this->admin)
            ->test(DisputeView::class, ['dispute_id' => $dispute->id])
            ->assertStatus(200);

        // Stranger access blocked with 403
        Livewire::actingAs($this->stranger)
            ->test(DisputeView::class, ['dispute_id' => $dispute->id])
            ->assertStatus(403);
    }

    public function test_offer_view_blocks_unauthorized_stranger_with_403(): void
    {
        $offer = Offer::create([
            'sender_id' => $this->seller->id,
            'recipient_id' => $this->buyer->id,
            'delivery_method' => 'buyer_responsible',
            'discount' => 5000,
            'status' => 'pending',
            'expires_at' => now()->addDays(2),
        ]);

        // Seller permitted
        Livewire::actingAs($this->seller)
            ->test(OfferView::class, ['offer_id' => $offer->id])
            ->assertStatus(200);

        // Buyer permitted
        Livewire::actingAs($this->buyer)
            ->test(OfferView::class, ['offer_id' => $offer->id])
            ->assertStatus(200);

        // Stranger blocked
        Livewire::actingAs($this->stranger)
            ->test(OfferView::class, ['offer_id' => $offer->id])
            ->assertStatus(403);
    }

    public function test_has_media_rejects_dangerous_file_extensions(): void
    {
        Storage::fake('public');

        $category = Category::firstOrCreate(['slug' => 'test-cat'], ['name' => 'Test Cat']);
        $model = DeviceModel::firstOrCreate(['slug' => 'test-mod'], ['name' => 'Test Mod', 'category_id' => $category->id]);
        $item = Item::create([
            'user_id' => $this->seller->id,
            'model_id' => $model->id,
            'condition_status' => 'working',
        ]);

        // Malicious file test 1: .php file
        $maliciousPhp = UploadedFile::fake()->create('backdoor.php', 100, 'application/x-php');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Security error: Uploaded file extension '.php' is not permitted.");

        $item->attachMedia($maliciousPhp);
    }

    public function test_has_media_accepts_valid_image_file(): void
    {
        Storage::fake('public');

        $category = Category::firstOrCreate(['slug' => 'test-cat2'], ['name' => 'Test Cat 2']);
        $model = DeviceModel::firstOrCreate(['slug' => 'test-mod2'], ['name' => 'Test Mod 2', 'category_id' => $category->id]);
        $item = Item::create([
            'user_id' => $this->seller->id,
            'model_id' => $model->id,
            'condition_status' => 'working',
        ]);

        $validPhoto = UploadedFile::fake()->image('clean_product.jpg', 600, 600);
        $media = $item->attachMedia($validPhoto);

        $this->assertNotNull($media);
        $this->assertEquals('image', $media->media_type);
        Storage::disk('public')->assertExists($media->file_path);
    }

    public function test_login_rate_limiter_locks_out_after_multiple_failures(): void
    {
        RateLimiter::clear('test_brute@example.com|127.0.0.1');

        $component = Livewire::test(Login::class)
            ->set('email', 'test_brute@example.com')
            ->set('password', 'wrong-pass-1')
            ->call('submit')
            ->assertSet('errorMessage', 'These credentials do not match our records.');

        // 4 more attempts
        for ($i = 2; $i <= 5; $i++) {
            $component->set('password', "wrong-pass-{$i}")->call('submit');
        }

        // 6th attempt: Must trigger rate limiter lockout
        $component->set('password', 'wrong-pass-6')
            ->call('submit');

        $errorMsg = $component->get('errorMessage');
        $this->assertStringContainsString('Too many login attempts', $errorMsg);
    }
}
