<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'phone_code',
        'currency',
        'currency_symbol',
        'timezone',
        'is_active',
        'is_default',
        'payment_gateway',
        'views',
        'clicks',
    ];
    

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'payment_gateway' => 'array',
            'views' => 'decimal:4',
            'clicks' => 'decimal:2',
        ];
    }

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
