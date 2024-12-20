<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $primaryKey = 'name';

    public $incrementing = false;

    protected $fillable = ['name', 'value'];

    public function value(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value, true),
            set: fn($value) => json_encode($value),
        );
    }
}
