<?php

namespace App\Models;

use EightyNine\Approvals\Models\ApprovableModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends ApprovableModel
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
