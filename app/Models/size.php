<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class size extends Model
{
        protected $primaryKey = 'size_id';
        protected $fillable =[
            'size_name',
            'size_status'
        ];
}
