<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // บอกว่า field ไหนกรอกได้    
protected $fillable =[
        'name',
        'slug',
        'description'
    ];

    //  Relationship: Category มีได้หลาย product
    public function products()
    {
        return $this->belongsToMany((Product::class));
    }
}

// $fillable คืออะไร?
// ป้องกัน Mass Assignment Attack 
// คือถ้าไม่ระบุ $fillable → ใครก็ส่ง field อะไรมาก็ได้
// เช่น แฮกเกอร์ส่ง role=admin มาด้วย → อันตราย
// $fillable บอกว่า → รับแค่ field เหล่านี้เท่านั้น