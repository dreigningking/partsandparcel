<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'provider_name',
        'tracking_number',
        'status',
        'origin_location_id',
        'origin_contact_name',
        'origin_contact_phone',
        'origin_address_line_1',
        'origin_address_line_2',
        'origin_city',
        'origin_state',
        'origin_country',
        'origin_postal_code',
        'origin_latitude',
        'origin_longitude',
        'destination_location_id',
        'destination_contact_name',
        'destination_contact_phone',
        'destination_address_line_1',
        'destination_address_line_2',
        'destination_city',
        'destination_state',
        'destination_country',
        'destination_postal_code',
        'destination_latitude',
        'destination_longitude',
        'fee',
        'dispatched_at',
        'delivered_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'fee' => 'decimal:2',
            'dispatched_at' => 'datetime',
            'delivered_at' => 'datetime',
            'origin_latitude' => 'decimal:7',
            'origin_longitude' => 'decimal:7',
            'destination_latitude' => 'decimal:7',
            'destination_longitude' => 'decimal:7',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function originLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'origin_location_id');
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_location_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }
}
