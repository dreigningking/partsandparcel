<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Discussion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'category_id',
        'model_id',
        'title',
        'body',
        'attachments',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function deviceModel(): BelongsTo
    {
        return $this->belongsTo(DeviceModel::class, 'model_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function conversations(): MorphMany
    {
        return $this->morphMany(Conversation::class, 'contextable');
    }

    public function scopeInCurrentCountry($query, ?string $countryCode = null)
    {
        $code = strtoupper($countryCode ?? session('current_location.country_code', 'NG'));

        return $query->whereHas('user', fn ($q) => $q->where('country_code', $code));
    }
}
