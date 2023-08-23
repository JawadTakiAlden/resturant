<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function setImageAttribute ($image){
        $newImageName = uniqid() . '_' . 'meal_image' . '.' . $image->extension();
        $image->move(public_path('meal_images') , $newImageName);
        return $this->attributes['image'] =  '/'.'meal_images'.'/' . $newImageName;
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

}
