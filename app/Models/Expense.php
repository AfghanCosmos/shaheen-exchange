<?php

namespace App\Models;

use EightyNine\Approvals\Models\ApprovableModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends ApprovableModel
{
    use HasFactory;
    // Relationships
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }


}
