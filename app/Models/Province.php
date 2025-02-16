<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }


    // Relationship: A province has many workshops
    public function workshops()
    {
        return $this->hasMany(Workshop::class, 'province_id');
    }
}
