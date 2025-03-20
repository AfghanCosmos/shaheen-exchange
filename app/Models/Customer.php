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

    public function workshopOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }


    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
