<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Tag extends Model
{
    public function contacts(): HasManyThrough
    {
        return $this->hasManyThrough(Contact::class, ContactTag::class);
    }
}
