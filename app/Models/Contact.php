<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Contact extends Model
{
    public function tags(): HasManyThrough
    {
        return $this->hasMany(Tag::class, ContactTag::class);
    }
}
