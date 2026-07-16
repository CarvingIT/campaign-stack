<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use SoftDeletes, HasFactory;
    protected $fillable = ['salutation','firstname','lastname','email','company','mobile'];

    public function contactTags(): HasMany
    {
        return $this->hasMany(ContactTag::class);
    }
    
    public function updateTags(array $tag_ids){
        // first remove all for this model
        ContactTag::where('contact_id', $this->id)->delete();
        // then add
        foreach($tag_ids as $tid){
            $ct = new ContactTag();
            $ct->tag_id = $tid;
            $ct->contact_id = $this->id;
            $ct->save();
        }
    }
}
