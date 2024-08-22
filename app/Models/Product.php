<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function setImageAttribute($image)
    {
        if ($image instanceof \Illuminate\Http\UploadedFile) {
            $newImageName = uniqid().'_'.'products_image'.'.'.$image->extension();
            $image->move(public_path('products_image'), $newImageName);
            $this->attributes['image'] = '/'.'products_image'.'/'.$newImageName;
        } elseif (is_string($image)) {
            $this->attributes['image'] = $image;
        }
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function size() {
        return $this->hasMany(Size::class);
    }
}
