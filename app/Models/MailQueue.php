<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MailQueue extends Model
{
    use HasFactory;
    public function newsletter(): BelongsTo
    {
        return $this->belongsTo(Newsletter::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function outboundMailAccount(): BelongsTo
    {
        return $this->belongsTo(
            OutboundMailAccount::class,
            'outbound_mail_account_id'
        );
    }
}