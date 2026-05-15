<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'title', 'message', 'type',
        'reference_type', 'reference_id', 'is_read', 'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update(['is_read' => true, 'read_at' => now()]);
        }
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'loan_status' => 'bg-blue-100 text-blue-600',
            'assignment'  => 'bg-purple-100 text-purple-600',
            'document'    => 'bg-orange-100 text-orange-600',
            default       => 'bg-gray-100 text-gray-500',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'loan_status' => 'document',
            'assignment'  => 'user',
            'document'    => 'paper-clip',
            default       => 'bell',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'loan_status' => 'Loan Status',
            'assignment'  => 'Assignment',
            'document'    => 'Document',
            default       => 'System',
        };
    }
}
