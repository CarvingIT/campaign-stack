<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    public function contacts(): HasManyThrough
    {
        return $this->hasManyThrough(Contact::class, ContactTag::class);
    }

     public function newsletters(): HasMany
    {
        return $this->hasMany(Newsletter::class);
    }

}
