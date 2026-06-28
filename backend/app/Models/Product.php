<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationship: Product อยู่ได้หลาย Category
    public function categories(){
        return $this->belongsToMany(Category::class);
    }  

    // Relationship: Product มี Stock 1 อัน
    public function inventory(){
        return $this->hasOne(Inventory::class);
    }

    // Relationship: Product ถูกสั่งซื้อหลายครั้ง
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}


// แปลงค่าจาก Database ให้เป็น Type ที่ถูกต้องอัตโนมัติ

// price     → decimal:2  หมายถึง 590.00 ไม่ใช่ "590.00" (string)
// is_active → boolean    หมายถึง true/false ไม่ใช่ 1/0

