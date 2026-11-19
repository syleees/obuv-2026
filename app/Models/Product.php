<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $primaryKey = "product_id";

    public $timestamps = false;

    protected $casts = [
        'price' => 'decimal:2'
    ];
    
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id','category_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class,'manufacturer_id','manufacturer_id');
    }

    public function stockItems()
    {
        return $this->hasMany(StockItem::class,'product_id','product_id');
    }
}
