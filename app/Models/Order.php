<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';

    public $timestamps = false;

    protected $casts = [
        'order_date' => 'date',
    ];
    protected $fillable = [
    'user_id','order_date',
];

public function items()
{
    return $this->hasMany(OrderItem::class,'order_id','order_id');
}
public function user()
{
    return $this->belongsTo(User::class, 'user_id', 'user_id');
}
}