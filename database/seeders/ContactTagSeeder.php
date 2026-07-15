<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContactTag;
use App\Models\Contact;
use App\Models\Tag;

class ContactTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $all_contacts = Contact::all();
        foreach($all_contacts as $c){
            try{
            ContactTag::factory()
            ->create(['contact_id' => $c->id, 'tag_id' => Tag::inRandomOrder()->first()->id ]);
            }
            catch(\Exception $e){
                // do nothing
            }
        }
    }
}
