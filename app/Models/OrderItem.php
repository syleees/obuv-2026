<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_item_id';

    public $timestamps = false;

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id','order_id');
    }
    public function stockItem()
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id', 'stock_item_id');
    }
protected $fillable = [
    'order_id','stock_item_id','quantity','unit_price',
];
protected $casts = [
    'unit_price' => 'decimal:2',
];
}