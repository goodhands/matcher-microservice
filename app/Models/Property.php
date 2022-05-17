<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = array(
        'fields', 'propertyType', 'name',
        'address'
    );

    protected $casts = array(
        'fields' => 'array'
    );

    public function getPropertyType()
    {
        return $this->propertyType;
    }

    public function getPropertyFields()
    {
        return $this->fields;
    }
}
