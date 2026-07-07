<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    public function newsletters(): HasMany
    {
        return $this->hasMany(Newsletter::class);
    }
}
