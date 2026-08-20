<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    public function contactTags(): HasMany
    {
        return $this->hasMany(ContactTag::class);
    }

    public function newsletters(): HasMany
    {
        return $this->hasMany(Newsletter::class);
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(
            Contact::class,
            'contact_tags',
            'tag_id',
            'contact_id'
        );
    }
}