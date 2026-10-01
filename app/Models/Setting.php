<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'value',
    'data_type',
])]
class Setting extends Model
{
    public static function getValue(string $name, mixed $default = null): mixed
    {
        $row = static::query()->where('name', $name)->first();

        return $row ? static::castStoredValue($row->value, $row->data_type) : $default;
    }

    public static function setValue(string $name, mixed $value, ?string $dataType = null): void
    {
        $type = $dataType ?? match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_array($value) => 'json',
            default => 'string',
        };

        $stored = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value, JSON_THROW_ON_ERROR),
            default => (string) $value,
        };

        static::query()->updateOrCreate(
            ['name' => $name],
            ['value' => $stored, 'data_type' => $type]
        );
    }

    protected static function castStoredValue(?string $raw, ?string $dataType): mixed
    {
        if ($raw === null) {
            return null;
        }

        return match ($dataType) {
            'boolean' => $raw === '1' || $raw === 'true',
            'integer' => (int) $raw,
            'json' => json_decode($raw, true) ?? [],
            default => $raw,
        };
    }
}
