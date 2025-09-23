<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','name','description','price','image_path',
        'price_discount','category_id','subcategory_id','brand','color','unit','size','sku','stock_qty','min_order_qty',
        'seo_title','seo_description','seo_keywords'
    ];

    public function user(){ return $this->belongsTo(User::class); }
}
