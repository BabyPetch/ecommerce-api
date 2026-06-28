<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'method',
        'status',
        'amount',
        'transaction_id',
        'paid_at',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    const STATUS_PENDING   = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED    = 'failed';
    const STATUS_REFUNDED  = 'refunded';

    const METHOD_PROMPTPAY = 'promptpay';
    const METHOD_STRIPE    = 'stripe';

    // Relationship: Payment เป็นของ Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Helper: เช็คว่าจ่ายสำเร็จไหม
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}