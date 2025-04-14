<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{

    protected $fillable = [
        'name',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function stores()
    {
        return $this->hasMany(\App\Models\Store::class);
    }

    public function branches()
    {
        return $this->hasMany(\App\Models\Branch::class);
    }
}
