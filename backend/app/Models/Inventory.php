<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'reserved',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reserved' => 'integer',
    ];

    // Relationship: Inventory เป็นของ Product ชิ้นนึง
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Helper: คำนวณ Stock ที่พร้อมขายจริงๆ
    public function getAvailableAttribute(): int
    {
        return $this->quantity - $this->reserved;
    }
}

// hasOne/hasMany  → ฝั่งที่ "มี"
// belongsTo       → ฝั่งที่ "เป็นของ"

// Product  hasOne  Inventory   → Product มี Inventory
// Inventory belongsTo Product  → Inventory เป็นของ Product

// getAvailableAttribute คืออะไร?
// นี่คือ Accessor ครับ — สร้าง field สมมติที่คำนวณจาก field จริง

// $inventory->available
// → Laravel เรียก getAvailableAttribute() อัตโนมัติ
// → คืนค่า quantity - reserved

// เช่น quantity=10, reserved=3
// → $inventory->available = 7  ← พร้อมขายจริงๆ 7 ชิ้น