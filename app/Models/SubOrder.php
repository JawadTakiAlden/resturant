<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubOrder extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function orderItems () {
        return $this->hasMany(OrderItem::class);
    }

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function table(){
        return $this->belongsTo(Table::class);
    }
}
