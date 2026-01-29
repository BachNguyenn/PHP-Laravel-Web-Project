<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'member_id',
        'amount',
        'payment_type',
        'start_date',
        'end_date',
        'note',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the member associated with this invoice.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the user who created this invoice.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate unique invoice ID with format: INV-YYMM-XXX
     */
    public static function generateInvoiceId(): string
    {
        $prefix = 'INV-' . date('ym') . '-';
        
        $lastInvoice = self::where('invoice_id', 'like', $prefix . '%')
            ->orderBy('invoice_id', 'desc')
            ->first();
        
        if ($lastInvoice) {
            $lastSeq = (int) substr($lastInvoice->invoice_id, -3);
            $newSeq = $lastSeq + 1;
        } else {
            $newSeq = 1;
        }
        
        return $prefix . str_pad($newSeq, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get payment type label.
     */
    public function getPaymentTypeLabelAttribute(): string
    {
        return $this->payment_type === 'cash' ? 'Tiền mặt' : 'Chuyển khoản';
    }
}
