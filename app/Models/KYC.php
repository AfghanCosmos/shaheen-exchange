<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class KYC extends Model
{
    /** @use HasFactory<\Database\Factories\KYCFactory> */
    use HasFactory, SoftDeletes;

    protected $casts = [
        'issue_date' => 'date',
        'expire_date' => 'date',
        'third_party_response' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
