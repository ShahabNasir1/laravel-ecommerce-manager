<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class brand extends Model
{

    // 1. tell laravel that it is the primary key
    protected $primaryKey = 'brand_id';

    // 2. Wich columns can be mass-assign
    protected $fillable = [
        'brand_name',
        'brand_status'
    ];
}
