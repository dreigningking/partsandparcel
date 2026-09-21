<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'business_name',
        'avatar',
        'bio',
        'is_verified',
        'theme_preference',
        'country_code',
        'currency',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
        ];
    }

    // Access checks
    public function isAdmin(): bool
    {
        return $this->role_id !== null;
    }

    public function hasPermission(string $permission): bool
    {
        if (! $this->isAdmin() || ! $this->role) {
            return false;
        }

        // If super admin has '*' or explicit permission
        return ! empty($this->role->permissions['*']) || ! empty($this->role->permissions[$permission]);
    }

    // Relationships
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

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->where('ends_at', '>', now());
    }

    public function serviceJobsAsCustomer(): HasMany
    {
        return $this->hasMany(ServiceJob::class, 'customer_id');
    }

    public function serviceJobsAsProvider(): HasMany
    {
        return $this->hasMany(ServiceJob::class, 'provider_id');
    }
}
