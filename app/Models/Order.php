<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function table() {
        return $this->belongsTo(Table::class);
    }

    public function orderItems(){
        return $this->hasMany(OrderItem::class);
    }
}
