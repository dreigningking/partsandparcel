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
        return $this->hasMany(ConversationMessage::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(ConversationMessage::class)->latestOfMany();
    }

    public function isSupport(): bool
    {
        return is_null($this->contextable_type) || in_array($this->contextable_type, ['user', User::class]);
    }

    public function scopeSupport($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('contextable_type')
              ->orWhereIn('contextable_type', ['user', User::class]);
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
}
