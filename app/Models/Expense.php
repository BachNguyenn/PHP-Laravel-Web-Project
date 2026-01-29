<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'date',
        'expense_type',
        'note',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    /**
     * Get the user who created this expense.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get expense type label.
     */
    public function getExpenseTypeLabelAttribute(): string
    {
        $labels = [
            'utilities' => 'Tiền điện/nước',
            'equipment' => 'Thiết bị',
            'salary' => 'Lương nhân viên',
            'maintenance' => 'Bảo trì',
            'other' => 'Khác',
        ];

        return $labels[$this->expense_type] ?? $this->expense_type;
    }
}
