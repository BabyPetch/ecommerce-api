<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'total_amount',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'note',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    // Status ที่อนุญาต
    const STATUS_PENDING_PAYMENT = 'pending_payment';
    const STATUS_PAID            = 'paid';
    const STATUS_PROCESSING      = 'processing';
    const STATUS_SHIPPING        = 'shipping';
    const STATUS_COMPLETED       = 'completed';
    const STATUS_CANCELLED       = 'cancelled';

    // Relationship: Order เป็นของ User คนนึง
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship: Order มีหลาย OrderItem
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relationship: Order มี Payment 1 อัน
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Helper: เช็คว่า Cancel ได้ไหม
    public function isCancellable(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING_PAYMENT,
            self::STATUS_PAID,
        ]);
    }
}


// อธิบาย
// const STATUS_... คืออะไร?
// แทนที่จะเขียน string ตรงๆ แบบนี้
// if ($order->status === 'pending_payment')  ← พิมพ์ผิดได้ง่าย

// ใช้ const แทน
// if ($order->status === Order::STATUS_PENDING_PAYMENT)  ← ปลอดภัยกว่า

// ถ้าพิมพ์ผิด PHP จะ Error ทันที
// ถ้าพิมพ์ string ผิด ไม่มี Error แต่ bug เงียบๆ
// isCancellable() คืออะไร?
// Business Logic ง่ายๆ ว่า Order นี้ Cancel ได้ไหม
// แทนที่จะเขียนเงื่อนไขซ้ำทุกที่
// เขียนครั้งเดียวใน Model ใช้ได้ทุกที่เลย

// $order->isCancellable()  ← อ่านเข้าใจทันที