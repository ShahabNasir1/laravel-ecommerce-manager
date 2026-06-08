<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class product_image extends Model
{
    protected $primaryKey = 'image_id';
    protected $fillable = [
        'product_id',
        'image_url',
        'sort_order'
    ];

    public function product()
    {
        return $this->belongsTo(product::class, 'product_id', 'product_id');
    }
}
