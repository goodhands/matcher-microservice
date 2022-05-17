<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchProfile extends Model
{
    use HasFactory;

    protected $fillable = array(
        'searchFields', 'propertyType', 'name'
    );

    protected $casts = array(
        'searchFields' => 'array'
    );
}
