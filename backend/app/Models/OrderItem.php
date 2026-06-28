<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'quantity'   => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
    ];

    // Relationship: OrderItem เป็นของ Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relationship: OrderItem อ้างถึง Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}