<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public function customerType()
    {
        return $this->belongsTo(CustomerType::class);
    }



    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }


}
