<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function setImageAttribute($image)
    {
        if ($image instanceof \Illuminate\Http\UploadedFile) {
            $newImageName = uniqid().'_'.'categories_image'.'.'.$image->extension();
            $image->move(public_path('categories_image'), $newImageName);
            $this->attributes['image'] = '/'.'categories_image'.'/'.$newImageName;
        } elseif (is_string($image)) {
            $this->attributes['image'] = $image;
        }
    }

    public function product() {
        return $this->hasMany(Product::class);
    }
}
