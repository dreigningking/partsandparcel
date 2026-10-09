<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'name', 'value', 'type', 'segment',
    ];

    protected static function booted(): void
    {
        static::saved(function (Setting $setting) {
            static::flushCache($setting->name);
        });

        static::deleted(function (Setting $setting) {
            static::flushCache($setting->name);
        });
    }

    public static function getValue(string $name, mixed $default = null): mixed
    {
        $cached = Cache::remember("app_setting:{$name}", now()->addHours(24), function () use ($name) {
            $row = static::query()->where('name', $name)->first();

            return $row ? ['value' => $row->value, 'type' => $row->type] : '__MISSING__';
        });

        if ($cached === '__MISSING__' || ! is_array($cached)) {
            return $default;
        }

        return static::castStoredValue($cached['value'], $cached['type']);
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

        static::flushCache($name);
    }

    public static function flushCache(?string $name = null): void
    {
        if ($name !== null) {
            Cache::forget("app_setting:{$name}");
        }
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
