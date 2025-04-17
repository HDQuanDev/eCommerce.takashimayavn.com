<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Model;

class ProductAdmin extends Model
{
    protected $table = 'products_admin';

    protected $fillable = [
        'name', 'added_by', 'user_id', 'category_id', 'brand_id', 'photos', 'thumbnail_img',
        'unit_price', 'purchase_price', 'discount', 'discount_type',
        'current_stock', 'unit', 'min_qty', 'tags', 'description'
    ];

    protected $casts = [
        'attributes' => 'array',
        'variations' => 'array',
        'choice_options' => 'array',
        'colors' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function getThumbnailImgAttribute($value)
    {
        if ($value && $value != "") {
            return uploaded_asset($value);
        }
        return static_asset('assets/img/placeholder.jpg');
    }

    public function getPhotosAttribute($value)
    {
        if ($value) {
            $photos = explode(',', $value);
            $result = [];
            foreach ($photos as $photo) {
                if ($photo && $photo != "") {
                    $result[] = uploaded_asset($photo);
                }
            }
            return $result;
        }
        return [];
    }

    public function getPhotoPathsAttribute()
    {
        if ($this->photos) {
            return json_decode($this->photos);
        }
        return [];
    }

    public function getThumbnailImgPathAttribute()
    {
        if ($this->thumbnail_img) {
            return uploaded_asset($this->thumbnail_img);
        }
        return null;
    }
}
