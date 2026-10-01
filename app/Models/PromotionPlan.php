<?php

namespace App\Models;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'country_id',
        'views',
        'clicks',
    ];

    protected function casts(): array
    {
        return [
            'views' => 'decimal:4',
            'clicks' => 'decimal:2',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
