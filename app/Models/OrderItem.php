<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function subOrder(){
        return $this->belongsTo(SubOrder::class);
    }

    public function meal(){
        return $this->belongsTo(Meal::class);
    }
}
