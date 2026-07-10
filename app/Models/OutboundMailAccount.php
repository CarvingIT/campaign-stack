<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutboundMailAccount extends Model
{
    use SoftDeletes;

    public function newsletters(): HasManyThrough
    {
        return $this->hasManyThrough(Newsletter::class, NewsletterOutboundMailAccount::class);
    }
}
