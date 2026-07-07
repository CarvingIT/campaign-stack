<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class OutboundMailAccount extends Model
{
    public function newsletters(): HasManyThrough
    {
        return $this->hasManyThrough(Newsletter::class, NewsletterOutboundMailAccount::class);
    }
}
