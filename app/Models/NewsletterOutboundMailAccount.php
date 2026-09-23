<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterOutboundMailAccount extends Model
{
    protected $fillable = [
        'newsletter_id',
        'outbound_mail_account_id',
        'priority',
    ];
}