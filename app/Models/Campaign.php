<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model
{
    use SoftDeletes, HasFactory;
    public function newsletters(): HasMany
    {
        return $this->hasMany(Newsletter::class);
    }
}
