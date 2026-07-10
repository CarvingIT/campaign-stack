<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Newsletter extends Model
{
    use SoftDeletes;
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    public function sent_mails(): HasMany
    {
        return $this->hasMany(SentMail::class);
    }

    public function outbound_mail_accounts(): HasManyThrough
    {
        return $this
            ->hasManyThrough(OutboundMailAccount::class, NewsletterOutboundMailAccount::class)
            ->orderBy('priority');
    }
}
