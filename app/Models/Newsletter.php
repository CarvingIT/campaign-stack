<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function newsletter_tags(): HasMany
    {
        return $this->hasMany(NewsletterTag::class);
    }

    public function sent_mails(): HasMany
    {
        return $this->hasMany(SentMail::class);
    }

    public function queued_mails(): HasMany
    {
        return $this->hasMany(MailQueue::class);
    }

    public function newsletter_outbound_mail_accounts(): HasMany
    {
        return $this
            ->hasMany(NewsletterOutboundMailAccount::class)
            ->orderBy('priority');
    }

    public function updateTags(array $tag_ids){
        // first remove all for this model
        NewsletterTag::where('newsletter_id', $this->id)->delete();
        // then add
        foreach($tag_ids as $tid){
            $nt = new NewsletterTag();
            $nt->tag_id = $tid;
            $nt->newsletter_id = $this->id;
            $nt->save();
        }
    }
}
