<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'reported_by',
        'type',
        'status',
        'description',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(IssueItem::class);
    }

    public function returnRecord(): HasOne
    {
        return $this->hasOne(ReturnRecord::class, 'issue_id');
    }

    public function replacement(): HasOne
    {
        return $this->hasOne(Replacement::class, 'issue_id');
    }

    public function dispute(): HasOne
    {
        return $this->hasOne(Dispute::class, 'issue_id');
    }
}
