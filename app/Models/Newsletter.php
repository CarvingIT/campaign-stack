<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Newsletter extends Model
{
    use SoftDeletes, HasFactory;
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

    public function outbound_mail_accounts(): BelongsToMany
    {
        return $this->belongsToMany(
            OutboundMailAccount::class,
            'newsletter_outbound_mail_accounts',
            'newsletter_id',
            'outbound_mail_account_id'
        )->withPivot('priority')->orderByPivot('priority');
    }

    public function updateTags(array $tag_ids = null){
        // first remove all for this model
        NewsletterTag::where('newsletter_id', $this->id)->delete();
        if (empty($tag_ids)) return;
        // then add
        foreach($tag_ids as $tid){
            $nt = new NewsletterTag();
            $nt->tag_id = $tid;
            $nt->newsletter_id = $this->id;
            $nt->save();
        }
    }

    public function updateOutboundAccounts(array $account_ids = null){
        NewsletterOutboundMailAccount::where('newsletter_id', $this->id)->delete();
        if (empty($account_ids)) return;
        $priority = 1;
        foreach($account_ids as $acc_id){
            $noma = new NewsletterOutboundMailAccount();
            $noma->newsletter_id = $this->id;
            $noma->outbound_mail_account_id = $acc_id;
            $noma->priority = $priority++;
            $noma->save();
        }
    }
}
