<?php

namespace App\Livewire\Marketplace;

use App\Models\Cart;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Listing;
use App\Models\Location;
use App\Models\Payment;
use App\Models\User;
use App\Services\Commercial\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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

    // Payment Option selection ('escrow' vs 'direct_seller')
    public $paymentMethod = 'escrow';

    // Fee Configuration
    public $escrowFee = 1500;

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
        $sellerParam = $seller ?? request()->query('seller');
        if ($sellerParam) {
            $this->sellerId = (string) $sellerParam;
        }

        $this->loadCheckoutData();
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
        $seller = User::with(['primaryLocation', 'bankAccounts'])->find($this->sellerId);

        if ($seller) {
            $this->sellerName = $seller->business_name ?: $seller->name;
            $this->sellerLocation = $seller->primaryLocation?->city
                ? "{$seller->primaryLocation->city}, {$seller->primaryLocation->state}"
                : 'Computer Village, Ikeja, Lagos';

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
            $locations = Location::where('user_id', $user->id)->get();
            if ($locations->isNotEmpty()) {
                $this->savedAddresses = $locations->toArray();
                $this->selectedAddressId = $locations->first()->id;
            }

            // Load real cart for this seller
            $cart = Cart::with(['items.listing'])
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
                    'icon' => '💻',
                ])->toArray();
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
                'icon' => '💻'
            ]
        ];
    }

    public function selectDeliveryMethod($method)
    {
        $this->deliveryMethod = in_array($method, ['pickup', 'seller_delivery']) ? $method : 'pickup';
    }

    public function setPaymentMethod($method)
    {
        $this->paymentMethod = $method;
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
        $isEscrow = ($this->paymentMethod === 'escrow');
        $activeEscrowFee = $isEscrow ? $this->escrowFee : 0;
        $totalPayable = $itemSubtotal + $activeEscrowFee;
        $commission = round($itemSubtotal * 0.05, 2);

        $cart = Cart::where('buyer_id', $user->id)->where('seller_id', $sellerId)->first();

        // Create the Invoice
        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
            'buyer_id' => $user->id,
            'seller_id' => $sellerId,
            'cart_id' => $cart?->id,
            'delivery_method' => $deliveryMethodMapped,
            'subtotal' => $itemSubtotal,
            'discount' => 0.00,
            'tax' => 0.00,
            'total' => $totalPayable,
            'payment_method' => $isEscrow ? 'platform' : 'direct',
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

        if ($isEscrow) {
            // Create pending payment
            Payment::create([
                'user_id' => $user->id,
                'invoice_id' => $invoice->id,
                'reference' => 'PAY-' . strtoupper(Str::random(12)),
                'provider' => 'paystack',
                'status' => 'pending',
                'amount' => $totalPayable,
                'currency' => $user->currency ?? 'NGN',
            ]);

            session()->flash('message', "Invoice {$invoice->invoice_number} created with Parts & Parcel Escrow protection! {$deliveryText}");
        } else {
            session()->flash('message', "Invoice {$invoice->invoice_number} generated for direct transfer. {$deliveryText} Please transfer directly to seller.");
        }

        return redirect()->route('invoices');
    }

    public function render()
    {
        $itemSubtotal = collect($this->cartItems)->sum(fn($i) => $i['price'] * $i['quantity']);
        $deliveryFee = 0;
        $activeEscrowFee = ($this->paymentMethod === 'escrow') ? $this->escrowFee : 0;
        $totalPayable = $itemSubtotal + $deliveryFee + $activeEscrowFee;

        return view('livewire.marketplace.checkout-page', [
            'itemSubtotal' => $itemSubtotal,
            'deliveryFee' => $deliveryFee,
            'activeEscrowFee' => $activeEscrowFee,
            'totalPayable' => $totalPayable
        ]);
    }
}