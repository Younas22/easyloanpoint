<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAssignment extends Model
{
    protected $fillable = [
        'customer_id', 'hr_id', 'assigned_by', 'notes',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function hr(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hr_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
