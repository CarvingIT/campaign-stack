<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;
    public function contact_tags(): HasMany
    {
        return $this->hasMany(ContactTag::class);
    }

     public function newsletters(): HasMany
    {
        return $this->hasMany(Newsletter::class);
    }

}
