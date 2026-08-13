<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OutboundMailAccount extends Model
{
    use SoftDeletes, HasFactory;

    public function newsletter_outbound_mail_accounts(): HasMany
    {
        return $this->hasMany(NewsletterOutboundMailAccount::class);
    }
}
