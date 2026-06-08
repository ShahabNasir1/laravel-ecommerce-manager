<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class color extends Model
{
    protected $primaryKey = 'color_id';
    protected $fillable = [
        'color_name',
        'color_status'
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            product::class,      // The related model
            'product_colors',    // The pivot table name
            'color_id',          // Foreign key on pivot table referencing this model (Color)
            'product_id'         // Foreign key on pivot table referencing the related model (Product)
        );
    }
}
