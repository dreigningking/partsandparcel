<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{

    protected $fillable = [
        'name','value','type','segment'
    ];
    public static function getValue(string $name, mixed $default = null): mixed
    {
        $row = static::query()->where('name', $name)->first();

        return $row ? static::castStoredValue($row->value, $row->type) : $default;
    }

    public static function setValue(string $name, mixed $value, ?string $type = null, ?string $segment = null): void
    {
        $type = $type ?? match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_array($value) => 'array',
            default => 'string',
        };

        $stored = match ($type) {
            'boolean' => ($value === true || $value === 1 || $value === '1' || $value === 'true') ? '1' : '0',
            'array', 'json' => is_string($value) ? $value : json_encode(array_values((array) $value), JSON_THROW_ON_ERROR),
            default => (string) $value,
        };

        $attributes = ['value' => $stored, 'type' => $type];
        if ($segment !== null) {
            $attributes['segment'] = $segment;
        }

        static::query()->updateOrCreate(
            ['name' => $name],
            $attributes
        );
    }

    protected static function castStoredValue(?string $raw, ?string $type): mixed
    {
        if ($raw === null) {
            return null;
        }

        return match ($type) {
            'boolean' => $raw === '1' || $raw === 'true',
            'integer' => (int) $raw,
            'array', 'json' => is_array($raw) ? $raw : (json_decode($raw, true) ?? (array) $raw),
            default => $raw,
        };
    }
}
