<?php

namespace App\Models\Product;

use App\Helper\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    const PRODUCT_IMAGE_DISK = 'product_img';

    protected $appends = [
        'full_url_image',
    ];

    protected $fillable = [
        'product_id',
        'path',
    ];

    public function getFullUrlImageAttribute()
    {
        return Helper::getFullPublicFileUrl($this->attributes['path']);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
