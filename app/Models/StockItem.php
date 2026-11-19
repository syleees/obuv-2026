<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'stock_item_id';

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','product_id');
    }
        public function orderItems()
    {
        return $this->hasMany(OrderItem::class,'stock_item_id','stock_item_id');
    }
    public function size()
    {
        return $this->belongsTo(Size::class,'size_id','size_id');
    }
}