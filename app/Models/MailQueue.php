<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailQueue extends Model
{
    public function newsletter(): BelongsTo
    {
        return $this->belongsTo(Newsletter::class);
    }
}
