<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactTag extends Model
{
    use HasFactory;
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}
