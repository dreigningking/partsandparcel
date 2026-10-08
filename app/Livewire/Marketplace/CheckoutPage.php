<?php

namespace App\Livewire\Marketplace;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Listing;
use App\Models\Location;
use App\Models\Payment;
use App\Models\User;
use App\Services\Commercial\CartService;
use App\Services\Payment\EscrowService;
use App\Services\Payment\FlutterwaveService;
use App\Services\Payment\PaystackService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CheckoutPage extends Component
{
    public $sellerId = '2';
    public $sellerName = 'Adam Computers';
    public $sellerLocation = 'Computer Village, Ikeja, Lagos';

    // Saved addresses
    public $savedAddresses = [];
    public $selectedAddressId = null;

    // Add new address inline
    public $showNewAddressModal = false;
    public $newAddressLabel = 'Home';
    public $newAddressLine = '';
    public $newCity = 'Ikeja';
    public $newState = 'Lagos';
    public $newPhone = '';

    // Delivery Handling Method selection ('pickup' vs 'seller_delivery')
    public $deliveryMethod = 'pickup';

    // Payment Option selection ('platform' vs 'direct')
    public $paymentMethod = 'platform';

    // Payment Gateway Provider selection ('paystack' vs 'flutterwave')
    public string $paymentProvider = 'paystack';

    // Fee Configuration (Computed from user subscription plan)
    public float $escrowPercentage = 10.0;
    public ?float $escrowCap = null;
    public float $escrowFee = 0.0;

    // Coupon / Promo Code State
    public string $couponCode = '';
    public ?int $appliedCouponId = null;
    public float $couponDiscount = 0.0;
    public string $couponMessage = '';
    public bool $couponValid = false;

    // Seller Bank Details for Direct Transfer
    public $sellerBank = [
        'bank_name' => 'GTBank (Guaranty Trust Bank)',
        'account_name' => 'Adam Computers Ltd',
        'account_number' => '0123456789'
    ];

    // Cart Items for Checkout
    public $cartItems = [];

    public function mount($seller = null)
    {
        if (! Auth::check()) {
            session()->put('url.intended', request()->fullUrl());
            session()->flash('warning', 'Please sign in to proceed with checkout.');
            return redirect()->route('login');
        }

        app(CartService::class)->mergeGuestCart(Auth::user());

        $sellerParam = $seller ?? request()->query('seller');
        if ($sellerParam) {
            $sellerModel = User::where('slug', $sellerParam)
                ->orWhere('id', is_numeric($sellerParam) ? (int) $sellerParam : null)
                ->first();
            if ($sellerModel) {
                $this->sellerId = (string) $sellerModel->id;
            } else {
                $this->sellerId = (string) $sellerParam;
            }
        }

        $this->paymentProvider = config('services.payment.default_gateway', 'paystack');

        $this->loadCheckoutData();
    }

    public function setPaymentProvider(string $provider): void
    {
        if (in_array($provider, ['paystack', 'flutterwave'])) {
            $this->paymentProvider = $provider;
        }
    }

    public function saveNewAddress()
    {
        if (trim($this->newAddressLine) === '') return;

        $user = Auth::user();
        if ($user) {
            $location = Location::create([
                'user_id' => $user->id,
                'name' => $this->newAddressLabel,
                'address_line_1' => $this->newAddressLine,
                'city' => $this->newCity,
                'state' => $this->newState,
                'country_code' => $user->country_code ?? 'NG',
                'phone' => $this->newPhone ?: $user->phone,
            ]);

            $this->loadCheckoutData();
            $this->selectedAddressId = $location->id;
        } else {
            $newId = count($this->savedAddresses) + 1;
            $this->savedAddresses[] = [
                'id' => $newId,
                'name' => $this->newAddressLabel,
                'address_line_1' => $this->newAddressLine,
                'city' => $this->newCity,
                'state' => $this->newState,
                'phone' => $this->newPhone,
            ];
            $this->selectedAddressId = $newId;
        }

        $this->showNewAddressModal = false;
        $this->newAddressLine = '';
    }

    public function loadCheckoutData()
    {
        $user = Auth::user();
        $seller = User::with(['primaryLocation.state', 'bankAccounts'])
            ->where('id', is_numeric($this->sellerId) ? (int) $this->sellerId : null)
            ->orWhere('slug', $this->sellerId)
            ->first();

        if ($seller) {
            $this->sellerId = (string) $seller->id;
            $this->sellerName = $seller->business_name ?: $seller->name;
            $loc = $seller->primaryLocation;
            $stateName = $loc?->state?->name ?? null;
            $this->sellerLocation = $loc
                ? collect([$loc->city, $stateName])->filter()->implode(', ')
                : 'Computer Village, Ikeja, Lagos';

            if (empty($this->sellerLocation)) {
                $this->sellerLocation = 'Computer Village, Ikeja, Lagos';
            }

            $activeBank = $seller->bankAccounts()->first();
            if ($activeBank) {
                $this->sellerBank = [
                    'bank_name' => $activeBank->bank_name,
                    'account_name' => $activeBank->account_name,
                    'account_number' => $activeBank->account_number,
                ];
            }
        }

        if ($user) {
            // Load saved addresses
            $locations = Location::with('state')->where('user_id', $user->id)->get();
            if ($locations->isNotEmpty()) {
                $this->savedAddresses = $locations->map(fn ($l) => [
                    'id' => $l->id,
                    'name' => $l->label ?: ($l->name ?: 'Address'),
                    'label' => $l->label ?: ($l->name ?: 'Address'),
                    'address_line_1' => $l->address_line_1,
                    'city' => $l->city,
                    'state' => $l->state?->name ?? '',
                    'phone' => $l->phone,
                ])->toArray();
                $this->selectedAddressId = $locations->first()->id;
            }

            // Load real cart for this seller
            $cart = Cart::with(['items.listing.item'])
                ->where('buyer_id', $user->id)
                ->where('seller_id', $this->sellerId)
                ->where('status', 'active')
                ->first();

            if ($cart && $cart->items->isNotEmpty()) {
                $this->cartItems = $cart->items->map(fn ($item) => [
                    'id' => $item->id,
                    'listing_id' => $item->listing_id,
                    'title' => $item->listing?->title ?? "Cart Item #{$item->id}",
                    'specs' => $item->listing?->condition ? ucfirst($item->listing->condition) : 'Standard',
                    'price' => (float) $item->unit_price,
                    'quantity' => (int) $item->quantity,
                    'icon' => $item->listing?->item?->item_type === 'part' ? '⚙️' : ($item->listing?->item?->item_type === 'scrap' ? '🛠️' : '💻'),
                    'allow_shipping' => (bool) ($item->listing?->allow_shipping ?? false),
                ])->toArray();

                if (! $this->canShip()) {
                    $this->deliveryMethod = 'pickup';
                }
                return;
            }
        }

        // Demo fallback if cart empty in DB
        $this->cartItems = [
            [
                'id' => 101,
                'listing_id' => 1,
                'title' => 'HP EliteBook 840 G5 Laptop',
                'specs' => 'Intel i5 · 8GB RAM · 256GB SSD',
                'price' => 280000,
                'quantity' => 1,
                'icon' => '💻',
                'allow_shipping' => true,
            ]
        ];

        if (! $this->canShip()) {
            $this->deliveryMethod = 'pickup';
        }

        $user = Auth::user();
        if ($user && method_exists($user, 'getEscrowPercentage')) {
            $this->escrowPercentage = (float) $user->getEscrowPercentage();
            $this->escrowCap = method_exists($user, 'getEscrowCap') ? $user->getEscrowCap() : null;
        }
        $subtotal = collect($this->cartItems)->sum(fn ($i) => $i['price'] * $i['quantity']);
        if ($user && method_exists($user, 'calculateEscrowFee')) {
            $this->escrowFee = $user->calculateEscrowFee((float) $subtotal);
        } else {
            $rawFee = round($subtotal * ($this->escrowPercentage / 100), 2);
            $this->escrowFee = ($this->escrowCap !== null && $rawFee > $this->escrowCap) ? (float) $this->escrowCap : $rawFee;
        }
    }

    #[Computed]
    public function canShip(): bool
    {
        return collect($this->cartItems)->contains(fn ($i) => ! empty($i['allow_shipping']));
    }

    public function selectDeliveryMethod($method)
    {
        if ($method === 'seller_delivery' && ! $this->canShip()) {
            return;
        }
        $this->deliveryMethod = in_array($method, ['pickup', 'seller_delivery']) ? $method : 'pickup';
    }

    public function setPaymentMethod($method)
    {
        $this->paymentMethod = in_array($method, ['platform', 'direct']) ? $method : 'platform';
        if ($this->paymentMethod === 'direct') {
            $this->removeCoupon();
        }
    }

    public function applyCoupon()
    {
        $this->resetErrorBag('couponCode');
        $this->couponMessage = '';

        $code = strtoupper(trim($this->couponCode));
        if (empty($code)) {
            $this->addError('couponCode', 'Please enter a coupon code.');
            return;
        }

        $coupon = Coupon::where('code', $code)->first();
        if (! $coupon) {
            $this->appliedCouponId = null;
            $this->couponDiscount = 0.0;
            $this->couponValid = false;
            $this->addError('couponCode', "Coupon '{$code}' is invalid or does not exist.");
            return;
        }

        $itemSubtotal = collect($this->cartItems)->sum(fn ($i) => $i['price'] * $i['quantity']);
        $eligibility = $coupon->validateEligibility($itemSubtotal);

        if (! $eligibility['valid']) {
            $this->appliedCouponId = null;
            $this->couponDiscount = 0.0;
            $this->couponValid = false;
            $this->addError('couponCode', $eligibility['message']);
            return;
        }

        $this->appliedCouponId = $coupon->id;
        $this->couponDiscount = $coupon->calculateDiscount($itemSubtotal);
        $this->couponValid = true;
        $this->couponMessage = "Coupon '{$coupon->code}' applied: " . ($coupon->type === 'percentage' ? "{$coupon->value}% off" : "₦" . number_format($coupon->value, 2) . " off");
    }

    public function removeCoupon()
    {
        $this->appliedCouponId = null;
        $this->couponDiscount = 0.0;
        $this->couponCode = '';
        $this->couponMessage = '';
        $this->couponValid = false;
        $this->resetErrorBag('couponCode');
    }

    public function placeOrder()
    {
        $user = Auth::user();
        if (! $user) {
            session()->flash('warning', 'Please sign in to complete your checkout.');
            return redirect()->route('login');
        }

        $sellerId = (int) $this->sellerId;
        $deliveryMethodMapped = ($this->deliveryMethod === 'seller_delivery') ? 'seller_responsible' : 'buyer_responsible';
        $itemSubtotal = collect($this->cartItems)->sum(fn ($i) => $i['price'] * $i['quantity']);
        $this->escrowPercentage = method_exists($user, 'getEscrowPercentage') ? (float) $user->getEscrowPercentage() : 10.0;
        $this->escrowCap = method_exists($user, 'getEscrowCap') ? $user->getEscrowCap() : null;
        if (method_exists($user, 'calculateEscrowFee')) {
            $this->escrowFee = $user->calculateEscrowFee((float) $itemSubtotal);
        } else {
            $rawFee = round($itemSubtotal * ($this->escrowPercentage / 100), 2);
            $this->escrowFee = ($this->escrowCap !== null && $rawFee > $this->escrowCap) ? (float) $this->escrowCap : $rawFee;
        }
        $isPlatform = in_array($this->paymentMethod, ['platform', 'escrow']);
        $activeEscrowFee = $isPlatform ? $this->escrowFee : 0;
        $discount = $isPlatform ? $this->couponDiscount : 0;
        $totalPayable = max(0.00, round($itemSubtotal + $activeEscrowFee - $discount, 2));
        $commission = round(max(0.00, $itemSubtotal - $discount) * 0.05, 2);

        $cart = Cart::where('buyer_id', $user->id)->where('seller_id', $sellerId)->first();

        $seller = User::with('country')->find($sellerId);
        $currency = $seller?->country?->currency ?: 'NGN';

        // Verify item availability before checkout
        foreach ($this->cartItems as $cItem) {
            if (! empty($cItem['listing_id'])) {
                $listing = Listing::find($cItem['listing_id']);
                if ($listing && $listing->availableQuantity() < (int) ($cItem['quantity'] ?? 1)) {
                    session()->flash('error', "Item '{$cItem['title']}' is no longer available in the requested quantity ({$listing->availableQuantity()} left). Please update your cart.");
                    return;
                }
            }
        }

        // Create the Invoice
        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
            'buyer_id' => $user->id,
            'seller_id' => $sellerId,
            'cart_id' => $cart?->id,
            'delivery_method' => $deliveryMethodMapped,
            'subtotal' => $itemSubtotal,
            'discount' => $discount,
            'tax' => 0.00,
            'total' => $totalPayable,
            'currency' => $currency,
            'payment_method' => $isPlatform ? 'platform' : 'direct',
            'commission' => $commission,
            'status' => 'issued',
            'issued_at' => now(),
            'due_at' => now()->addDays(3),
        ]);

        // Create Invoice Items
        foreach ($this->cartItems as $cItem) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'itemable_id' => $cItem['listing_id'] ?? null,
                'itemable_type' => ! empty($cItem['listing_id']) ? Listing::class : null,
                'type' => 'item',
                'description' => $cItem['title'],
                'quantity' => $cItem['quantity'],
                'unit_price' => $cItem['price'],
                'amount' => $cItem['price'] * $cItem['quantity'],
                'warranty_period_days' => 14,
                'warranty_terms' => 'Standard seller inspection warranty',
            ]);

            if (! empty($cItem['listing_id'])) {
                $listing = Listing::find($cItem['listing_id']);
                if ($listing) {
                    if ($isPlatform) {
                        $listing->reserved_quantity = ($listing->reserved_quantity ?? 0) + (int) ($cItem['quantity'] ?? 1);
                    } else {
                        $listing->sold_quantity = ($listing->sold_quantity ?? 0) + (int) ($cItem['quantity'] ?? 1);
                    }
                    $listing->save();
                }
            }
        }

        // Record coupon usage if applied
        if ($this->appliedCouponId) {
            $coupon = Coupon::find($this->appliedCouponId);
            $coupon?->recordUsage();
        }

        // Clear cart for this seller
        app(CartService::class)->clearSellerCart($user, $sellerId);

        // Notify parties
        $user->notify(new \App\Notifications\InvoiceIssuedNotification($invoice));
        $seller = User::find($sellerId);
        if ($seller && $seller->id !== $user->id) {
            $seller->notify(new \App\Notifications\InvoiceIssuedNotification($invoice));
        }

        $deliveryText = ($this->deliveryMethod === 'seller_delivery') 
            ? 'Seller delivery shipment recorded.' 
            : 'Buyer pickup selected (no shipment necessary).';

        if ($isPlatform) {
            $provider = in_array($this->paymentProvider, ['paystack', 'flutterwave'])
                ? $this->paymentProvider
                : config('services.payment.default_gateway', 'paystack');

            $reference = 'PAY-' . strtoupper(Str::random(12));

            // Only platform payments are recorded in the payments table
            $payment = Payment::create([
                'user_id' => $user->id,
                'paymentable_id' => $invoice->id,
                'paymentable_type' => Invoice::class,
                'reference' => $reference,
                'provider' => $provider,
                'status' => 'pending',
                'amount' => $totalPayable,
                'escrow_fee' => $this->escrowFee,
                'currency' => $user->currency ?? $currency ?? 'NGN',
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'coupon_code' => $this->appliedCouponId ? Coupon::find($this->appliedCouponId)?->code : null,
                    'discount' => $discount,
                    'escrow_fee' => $this->escrowFee,
                    'delivery_method' => $this->deliveryMethod,
                ],
            ]);

            // If 100% discount, mark successful without payment gateway
            if ($totalPayable <= 0.0) {
                app(EscrowService::class)->handlePaymentSuccessful($payment);
                session()->flash('buyer_payment_success', "Invoice {$invoice->invoice_number} paid via promo discount with Escrow Protection! {$deliveryText}");
                return redirect()->route('invoices.view', $invoice->id);
            }

            // Redirect user to payment gateway
            $callbackUrl = route('payment.callback', [
                'reference' => $reference,
                'provider' => $provider,
            ]);

            $authorizationUrl = null;
            $gatewayKey = config("services.{$provider}.secret");

            if (empty($gatewayKey) && (app()->isLocal() || app()->environment('testing'))) {
                $authorizationUrl = route('payment.callback', [
                    'reference' => $reference,
                    'provider' => $provider,
                    'mock_success' => 1,
                ]);
            } else {
                try {
                    if ($provider === 'flutterwave') {
                        $response = app(FlutterwaveService::class)->initialize($payment, $callbackUrl);
                        $authorizationUrl = $response['link'] ?? $response['authorization_url'] ?? null;
                    } else {
                        $response = app(PaystackService::class)->initialize($payment, $callbackUrl);
                        $authorizationUrl = $response['authorization_url'] ?? null;
                    }
                } catch (\Throwable $e) {
                    session()->flash('error', 'Failed to communicate with payment gateway: ' . $e->getMessage());
                    return redirect()->route('invoices.view', $invoice->id);
                }
            }

            if ($authorizationUrl) {
                return redirect()->away($authorizationUrl);
            }

            session()->flash('error', 'Could not initialize payment with ' . ucfirst($provider) . '. Please proceed to the invoice to complete payment.');
            return redirect()->route('invoices.view', $invoice->id);
        } else {
            // Direct payments happen directly between buyer and seller; not recorded in payments table
            session()->flash('message', "Invoice {$invoice->invoice_number} generated for direct transfer. {$deliveryText} Please transfer directly to seller's bank account.");
            return redirect()->route('invoices.view', $invoice->id);
        }
    }

    public function render()
    {
        $user = Auth::user();
        if ($user && method_exists($user, 'getEscrowPercentage')) {
            $this->escrowPercentage = (float) $user->getEscrowPercentage();
            $this->escrowCap = method_exists($user, 'getEscrowCap') ? $user->getEscrowCap() : null;
        }
        $itemSubtotal = collect($this->cartItems)->sum(fn($i) => $i['price'] * $i['quantity']);
        $deliveryFee = 0;
        if ($user && method_exists($user, 'calculateEscrowFee')) {
            $this->escrowFee = $user->calculateEscrowFee((float) $itemSubtotal);
        } else {
            $rawFee = round($itemSubtotal * ($this->escrowPercentage / 100), 2);
            $this->escrowFee = ($this->escrowCap !== null && $rawFee > $this->escrowCap) ? (float) $this->escrowCap : $rawFee;
        }
        $activeEscrowFee = ($this->paymentMethod === 'platform') ? $this->escrowFee : 0;
        $discount = ($this->paymentMethod === 'platform') ? $this->couponDiscount : 0;
        $totalPayable = max(0.00, round($itemSubtotal + $deliveryFee + $activeEscrowFee - $discount, 2));

        return view('livewire.marketplace.checkout-page', [
            'itemSubtotal' => $itemSubtotal,
            'deliveryFee' => $deliveryFee,
            'escrowPercentage' => $this->escrowPercentage,
            'escrowCap' => $this->escrowCap,
            'escrowFee' => $this->escrowFee,
            'activeEscrowFee' => $activeEscrowFee,
            'discount' => $discount,
            'totalPayable' => $totalPayable,
            'canShip' => $this->canShip(),
        ]);
    }
}