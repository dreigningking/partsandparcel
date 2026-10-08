<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Sluggable;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'role_id',
        'phone',
        'business_name',
        'slug',
        'avatar',
        'bio',
        'is_verified',
        'suspended_at',
        'gender',
        'notification_preferences',
        'country_id',
        'last_abandoned_cart_email_at',
        'freeze_payout',
    ];

    protected $attributes = [
        'notification_preferences' => '{"email":true,"in_app":true,"push":true}',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'slug_source',
            ],
        ];
    }

    public function getSlugSourceAttribute(): string
    {
        return ! empty($this->business_name) ? $this->business_name : ($this->name ?? 'user');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field = $field ?? $this->getRouteKeyName();

        if ($field === 'slug' || $field === null) {
            return $this->where('slug', $value)
                ->orWhere('id', is_numeric($value) ? (int) $value : null)
                ->first();
        }

        return parent::resolveRouteBinding($value, $field);
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->country_id)) {
                $user->country_id = Country::where('is_default', true)->value('id')
                    ?? Country::value('id')
                    ?? Country::firstOrCreate(
                        ['code' => 'NG'],
                        [
                            'name' => 'Nigeria',
                            'phone_code' => '+234',
                            'currency' => 'NGN',
                            'currency_symbol' => '₦',
                            'timezone' => 'Africa/Lagos',
                            'is_default' => true,
                            'is_active' => true,
                        ]
                    )->id;
            }
            if (empty($user->notification_preferences)) {
                $user->notification_preferences = ['email' => true, 'in_app' => true, 'push' => true];
            }
        });
    }

    public function getNotificationPreferencesAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        return $value ? (json_decode($value, true) ?: ['email' => true, 'in_app' => true, 'push' => true]) : ['email' => true, 'in_app' => true, 'push' => true];
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'suspended_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'freeze_payout' => 'boolean',
            'last_abandoned_cart_email_at' => 'datetime',
            'notification_preferences' => 'array',
        ];
    }

    public function isSuspended(): bool
    {
        return ! is_null($this->suspended_at);
    }

    public function notificationPreference(string $channel): bool
    {
        $prefs = $this->notification_preferences;
        if (is_string($prefs)) {
            $prefs = json_decode($prefs, true);
        }
        if (! is_array($prefs)) {
            return true;
        }
        return (bool) ($prefs[$channel] ?? true);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->avatar) {
            return null;
        }
        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }
        return asset('storage/' . $this->avatar);
    }

    // Access checks
    public function isAdmin(): bool
    {
        return $this->role_id !== null;
    }

    public function isSuperAdmin(): bool
    {
        if (! $this->isAdmin() || ! $this->role) {
            return false;
        }

        return in_array($this->role->slug, ['super_admin', 'super-admin', 'admin'], true) || $this->role->hasPermission('*');
    }

    public function hasPermission(string $permission): bool
    {
        if (! $this->isAdmin() || ! $this->role) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->role->hasPermission($permission);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        if (! $this->isAdmin() || ! $this->role) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($this->role->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve or create the default Customer Support account used for automated onboarding chats.
     */
    public static function getSupportUser(): self
    {
        $supportRole = Role::whereIn('slug', ['customer_support', 'super_admin'])->first();

        return static::where('email', 'support@partsandparcel.com')
            ->orWhere('role_id', $supportRole?->id)
            ->first() ?? static::firstOrCreate(
                ['email' => 'support@partsandparcel.com'],
                [
                    'name' => 'Parts & Parcel Support',
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32)),
                    'role_id' => $supportRole?->id,
                    'email_verified_at' => now(),
                ]
            );
    }

    // Relationships
    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function primaryLocation(): HasOne
    {
        return $this->hasOne(Location::class)->where('is_default', true);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function buyerCarts(): HasMany
    {
        return $this->hasMany(Cart::class, 'buyer_id');
    }

    public function sellerCarts(): HasMany
    {
        return $this->hasMany(Cart::class, 'seller_id');
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    public function sentOffers(): HasMany
    {
        return $this->hasMany(Offer::class, 'sender_id');
    }

    public function receivedOffers(): HasMany
    {
        return $this->hasMany(Offer::class, 'recipient_id');
    }

    public function buyerInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'buyer_id');
    }

    public function sellerInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'seller_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class, 'seller_id');
    }

    public static function currencySymbol(string $currency): string
    {
        return match (strtoupper($currency)) {
            'NGN' => '₦',
            'USD' => '$',
            'GBP' => '£',
            'EUR' => '€',
            'GHS' => 'GH₵',
            'KES' => 'KSh',
            'ZAR' => 'R',
            default => $currency,
        };
    }

    /**
     * Get user's earnings from settlements grouped by currency.
     */
    public function getEarningsByCurrency(): array
    {
        $settlements = $this->settlements()
            ->select('currency', 'status', \Illuminate\Support\Facades\DB::raw('SUM(amount) as total_amount'), \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
            ->groupBy('currency', 'status')
            ->get();

        $grouped = [];
        foreach ($settlements as $s) {
            $curr = strtoupper($s->currency ?: 'NGN');
            if (! isset($grouped[$curr])) {
                $grouped[$curr] = [
                    'currency' => $curr,
                    'symbol' => static::currencySymbol($curr),
                    'total' => 0.0,
                    'settled' => 0.0,
                    'eligible' => 0.0,
                    'pending' => 0.0,
                    'count' => 0,
                ];
            }
            $amount = (float) $s->total_amount;
            $grouped[$curr]['total'] += $amount;
            $grouped[$curr]['count'] += (int) $s->count;
            if ($s->status === 'settled') {
                $grouped[$curr]['settled'] += $amount;
            } elseif ($s->status === 'eligible') {
                $grouped[$curr]['eligible'] += $amount;
            } elseif ($s->status === 'pending') {
                $grouped[$curr]['pending'] += $amount;
            }
        }

        return $grouped;
    }

    /**
     * Get user's spendings from payments (and direct paid invoices) grouped by currency.
     */
    public function getSpendingsByCurrency(): array
    {
        $payments = $this->payments()
            ->whereIn('status', ['successful', 'paid', 'held_in_escrow'])
            ->select('currency', \Illuminate\Support\Facades\DB::raw('SUM(amount) as total_amount'), \Illuminate\Support\Facades\DB::raw('SUM(escrow_fee) as total_escrow_fee'), \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
            ->groupBy('currency')
            ->get();

        $grouped = [];
        foreach ($payments as $p) {
            $curr = strtoupper($p->currency ?: 'NGN');
            $grouped[$curr] = [
                'currency' => $curr,
                'symbol' => static::currencySymbol($curr),
                'total' => (float) $p->total_amount,
                'escrow_fee' => (float) ($p->total_escrow_fee ?? 0),
                'count' => (int) $p->count,
            ];
        }

        $directInvoices = $this->buyerInvoices()
            ->where('payment_method', 'direct')
            ->where('status', 'paid')
            ->whereDoesntHave('payments', function ($q) {
                $q->whereIn('status', ['successful', 'paid', 'held_in_escrow']);
            })
            ->select('currency', \Illuminate\Support\Facades\DB::raw('SUM(total) as total_amount'), \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
            ->groupBy('currency')
            ->get();

        foreach ($directInvoices as $inv) {
            $curr = strtoupper($inv->currency ?: 'NGN');
            if (! isset($grouped[$curr])) {
                $grouped[$curr] = [
                    'currency' => $curr,
                    'symbol' => static::currencySymbol($curr),
                    'total' => 0.0,
                    'escrow_fee' => 0.0,
                    'count' => 0,
                ];
            }
            $grouped[$curr]['total'] += (float) $inv->total_amount;
            $grouped[$curr]['count'] += (int) $inv->count;
        }

        return $grouped;
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class, 'seller_id');
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function defaultBankAccount(): HasOne
    {
        return $this->hasOne(BankAccount::class)->where('is_default', true);
    }

    public function deviceTokens(): HasMany
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(Verification::class);
    }


    public function verification(): HasOne
    {
        return $this->hasOne(Verification::class)->latestOfMany();
    }

    public function latestVerification(): HasOne
    {
        return $this->verification();
    }

    public function isIdentityVerified(): bool
    {
        return (bool) ($this->verification && $this->verification->isVerified());
    }

    public function isFacialVerified(): bool
    {
        return (bool) ($this->verification && $this->verification->isVerified() && $this->verification->selfie_image);
    }

    public function areLocationsVerified(): bool
    {
        $locations = $this->locations;
        if ($locations->isEmpty()) {
            return false;
        }

        return $locations->every(fn($loc) => $loc->isVerified());
    }

    public function getIsFullyVerifiedAttribute(): bool
    {
        return $this->isIdentityVerified() && $this->areLocationsVerified();
    }

    public function isFullyVerified(): bool
    {
        return $this->is_fully_verified;
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            });
    }

    /**
     * Get dynamic escrow fee percentage based on the user's active subscription plan.
     */
    public function getEscrowPercentage(): float
    {
        $activeSub = $this->activeSubscription()->with('plan')->first();
        if ($activeSub && $activeSub->plan && $activeSub->plan->escrow_percentage !== null) {
            return (float) $activeSub->plan->escrow_percentage;
        }

        $defaultPlan = SubscriptionPlan::where('is_default', true)->first()
            ?? SubscriptionPlan::where('name', 'like', '%Starter%')->first()
            ?? SubscriptionPlan::first();

        return $defaultPlan && $defaultPlan->escrow_percentage !== null 
            ? (float) $defaultPlan->escrow_percentage 
            : 10.00;
    }

    /**
     * Get dynamic escrow fee cap based on the user's active subscription plan.
     */
    public function getEscrowCap(): ?float
    {
        $activeSub = $this->activeSubscription()->with('plan')->first();
        if ($activeSub && $activeSub->plan && $activeSub->plan->escrow_cap !== null) {
            return (float) $activeSub->plan->escrow_cap;
        }

        $defaultPlan = SubscriptionPlan::where('is_default', true)->first()
            ?? SubscriptionPlan::where('name', 'like', '%Starter%')->first()
            ?? SubscriptionPlan::first();

        return $defaultPlan && $defaultPlan->escrow_cap !== null 
            ? (float) $defaultPlan->escrow_cap 
            : null;
    }

    /**
     * Calculate dynamic escrow fee for a given amount, applying escrow percentage and capping at escrow_cap if configured.
     */
    public function calculateEscrowFee(float $amount): float
    {
        $percentage = $this->getEscrowPercentage();
        $rawFee = round($amount * ($percentage / 100), 2);
        $cap = $this->getEscrowCap();

        if ($cap !== null && $rawFee > $cap) {
            return (float) $cap;
        }

        return (float) $rawFee;
    }

    public function serviceJobsAsCustomer(): HasMany
    {
        return $this->hasMany(ServiceJob::class, 'customer_id');
    }

    public function serviceJobsAsProvider(): HasMany
    {
        return $this->hasMany(ServiceJob::class, 'provider_id');
    }

    public function listingReviews(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(ListingReview::class, Listing::class, 'user_id', 'listing_id');
    }

    public function serviceReviews(): HasMany
    {
        return $this->hasMany(ServiceReview::class, 'provider_id');
    }

    public function reviewsWritten(): HasMany
    {
        return $this->hasMany(ListingReview::class, 'user_id');
    }
}
