<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getPermissionCountAttribute(): int
    {
        if (empty($this->permissions)) {
            return 0;
        }

        if (array_is_list($this->permissions)) {
            return count($this->permissions);
        }

        return count(array_filter($this->permissions));
    }

    public function hasPermission(string $permission): bool
    {
        if (empty($this->permissions)) {
            return false;
        }

        if (! empty($this->permissions['*'])) {
            return true;
        }

        if (array_is_list($this->permissions)) {
            return in_array($permission, $this->permissions, true) || in_array('*', $this->permissions, true);
        }

        return ! empty($this->permissions[$permission]);
    }
}
