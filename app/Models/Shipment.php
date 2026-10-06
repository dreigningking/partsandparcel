<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
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
        'evidence',
    ];

    protected static function booted(): void
    {
        static::creating(function (Shipment $shipment) {
            if (empty($shipment->slug)) {
                $base = ! empty($shipment->tracking_number)
                    ? Str::slug($shipment->tracking_number)
                    : 'shipment-' . strtolower(Str::random(8));

                $slug = $base;
                while (static::where('slug', $slug)->exists()) {
                    $slug = "{$base}-" . strtolower(Str::random(4));
                }
                $shipment->slug = $slug;
            }
        });
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

    public function getFormattedOriginAddressAttribute(): string
    {
        $parts = array_filter([
            $this->origin_address_line_1,
            $this->origin_address_line_2,
            $this->origin_city,
            $this->origin_state,
            $this->origin_country,
        ]);
        return implode(', ', $parts) ?: 'Not specified';
    }

    public function getFormattedDestinationAddressAttribute(): string
    {
        $parts = array_filter([
            $this->destination_address_line_1,
            $this->destination_address_line_2,
            $this->destination_city,
            $this->destination_state,
            $this->destination_country,
        ]);
        return implode(', ', $parts) ?: 'Not specified';
    }

    public function getEvidenceListAttribute(): array
    {
        if (empty($this->evidence)) {
            return [];
        }

        $raw = trim($this->evidence);

        // Check if JSON
        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $list = [];
            foreach ($decoded as $item) {
                if (is_string($item)) {
                    $list[] = $this->resolveEvidenceEntry($item);
                } elseif (is_array($item)) {
                    $url = $item['url'] ?? $item['path'] ?? $item['file'] ?? '';
                    if ($url) {
                        $list[] = [
                            'url' => $this->formatEvidenceUrl($url),
                            'title' => $item['title'] ?? $item['name'] ?? basename($url),
                            'is_image' => $this->isImageExtension($url),
                        ];
                    }
                }
            }
            return array_filter($list);
        }

        // Check comma or newline separated
        if (str_contains($raw, ',') || str_contains($raw, "\n")) {
            $parts = preg_split('/[,\r\n]+/', $raw);
            $list = [];
            foreach ($parts as $part) {
                $part = trim($part);
                if ($part !== '') {
                    $list[] = $this->resolveEvidenceEntry($part);
                }
            }
            return array_filter($list);
        }

        return [$this->resolveEvidenceEntry($raw)];
    }

    protected function resolveEvidenceEntry(string $path): array
    {
        $path = trim($path);
        return [
            'url' => $this->formatEvidenceUrl($path),
            'title' => basename($path),
            'is_image' => $this->isImageExtension($path),
            'raw' => $path,
        ];
    }

    protected function formatEvidenceUrl(string $path): string
    {
        $path = trim($path);
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return asset('storage/' . ltrim($path, '/'));
    }

    protected function isImageExtension(string $path): bool
    {
        $cleanPath = parse_url($path, PHP_URL_PATH) ?? $path;
        $extension = strtolower(pathinfo($cleanPath, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
    }
}
