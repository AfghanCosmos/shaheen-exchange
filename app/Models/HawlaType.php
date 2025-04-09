<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HawlaType extends Model
{
    /** @use HasFactory<\Database\Factories\HawlaTypeFactory> */
    use HasFactory;


    public function hawlas() {
        return $this->hasMany(Hawla::class, 'hawla_type_id');    }
}
