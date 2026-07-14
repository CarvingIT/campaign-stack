<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutboundMailAccount extends Model
{
    use SoftDeletes;

    public function newsletter_outbound_mail_accounts(): HasMany
    {
        return $this->hasMany(NewsletterOutboundMailAccount::class);
    }
}
