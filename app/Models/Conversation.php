<?php

namespace App\Models;

use App\Models\ConversationMessage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'contextable_type',
        'contextable_id',
        'created_by',
    ];

    public function contextable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ConversationMessage::class)->orderBy('created_at', 'asc')->orderBy('id', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(ConversationMessage::class)->latestOfMany();
    }

    public function isSupport(): bool
    {
        if (in_array($this->contextable_type, ['support'])) {
            return true;
        }

        if (in_array($this->contextable_type, ['user', User::class])) {
            return true;
        }

        if (is_null($this->contextable_type)) {
            $supportUser = User::getSupportUser();
            if ($supportUser && $this->participants->contains('user_id', $supportUser->id)) {
                return true;
            }

            return $this->participants->contains(function ($participant) {
                $user = $participant->user ?? User::find($participant->user_id);
                return $user && $user->isAdmin();
            });
        }

        return false;
    }

    public function scopeSupport($query)
    {
        return $query->where(function ($q) {
            $q->whereIn('contextable_type', ['user', User::class, 'support'])
              ->orWhere(function ($sub) {
                  $sub->whereNull('contextable_type')
                      ->where(function ($partQuery) {
                          $supportUser = User::getSupportUser();
                          if ($supportUser) {
                              $partQuery->where('created_by', $supportUser->id)
                                        ->orWhereHas('participants', fn($p) => $p->where('user_id', $supportUser->id));
                          }
                          $partQuery->orWhereHas('participants.user', function ($u) {
                              $u->whereNotNull('role_id');
                          });
                      });
              });
        });
    }

    /**
     * Resolve the customer user associated with this support conversation.
     */
    public function getCustomerUserAttribute(): ?User
    {
        if ($this->contextable instanceof User) {
            return $this->contextable;
        }

        $supportUser = User::getSupportUser();
        $participant = $this->participants->firstWhere('user_id', '!=', $supportUser->id);

        return $participant?->user ?? $this->creator;
    }

    /**
     * Resolve the other participant in this conversation relative to a user.
     */
    public function getOtherParticipant(?User $currentUser = null): ?User
    {
        $currentUserId = $currentUser?->id ?? \Illuminate\Support\Facades\Auth::id();
        $participant = $this->participants->firstWhere('user_id', '!=', $currentUserId);

        return $participant?->user;
    }

    /**
     * Resolve the display name of the other party.
     */
    public function getOtherPartyName(?User $currentUser = null): string
    {
        $user = $currentUser ?? \Illuminate\Support\Facades\Auth::user();

        if ($this->isSupport()) {
            if ($user && $user->isAdmin()) {
                $customer = $this->customer_user;
                return $customer?->business_name ?: $customer?->name ?: 'Customer';
            }
            return 'Customer Support';
        }

        $other = $this->getOtherParticipant($user);
        if ($other) {
            return $other->business_name ?: $other->name ?: 'Vendor';
        }

        if ($this->contextable && isset($this->contextable->title)) {
            return $this->contextable->title;
        }

        return 'Direct Inquiry';
    }
}
