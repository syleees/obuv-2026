<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $primaryKey = 'size_id';

    public $timestamps = false;

    public function stockItems()
    {
        return $this->hasMany(StockItem::class,'size_id','size_id');
    }
}