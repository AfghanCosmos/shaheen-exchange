<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    // Relationship: A workshop order belongs to a workshop
    public function manager()
    {
        return $this->belongsTo(User::class);
    }
}
