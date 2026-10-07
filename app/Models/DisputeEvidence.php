<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisputeEvidence extends Model
{
    use HasFactory;

    protected $table = 'dispute_evidence';

    protected $fillable = [
        'dispute_id',
        'requested_by',
        'target_party',
        'target_user_id',
        'title',
        'instructions',
        'deadline_preset',
        'deadline_at',
        'status',
        'party_notes',
        'files',
        'submitted_at',
        'submitted_by',
    ];

    protected function casts(): array
    {
        return [
            'files' => 'array',
            'deadline_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    public function dispute(): BelongsTo
    {
        return $this->belongsTo(Dispute::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function formattedDeadline(): string
    {
        if ($this->deadline_at) {
            return 'Due ' . $this->deadline_at->format('M d, Y · H:i');
        }

        return match ($this->deadline_preset) {
            '12_hours' => 'Within 12 Hours',
            '48_hours' => 'Within 48 Hours',
            default => 'Within 24 Hours',
        };
    }

    public function targetLabel(): string
    {
        if ($this->targetUser) {
            $roleLabel = $this->target_party === 'buyer' ? 'Buyer' : 'Seller';
            return "{$this->targetUser->name} ({$roleLabel})";
        }

        return $this->target_party === 'buyer' ? 'Buyer' : 'Seller';
    }
}
