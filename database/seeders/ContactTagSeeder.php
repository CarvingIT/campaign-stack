<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\ContactTag;

class ContactTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = Tag::all()->keyBy('label');

        $contacts = Contact::all();

        foreach ($contacts as $contact) {
            $assignedTagIds = [];

            // Intelligent tag mapping based on company / title / email
            $company = strtolower($contact->company ?? '');
            $email = strtolower($contact->email ?? '');
            $salutation = strtolower($contact->salutation ?? '');

            if (str_contains($email, '.ac.in') || str_contains($email, '.edu') || str_contains($salutation, 'dr.') || str_contains($salutation, 'prof.') || str_contains($company, 'iit') || str_contains($company, 'iim') || str_contains($company, 'library') || str_contains($company, 'university')) {
                if (isset($tags['Academic & Library Directors'])) {
                    $assignedTagIds[] = $tags['Academic & Library Directors']->id;
                }
                if (isset($tags['Koha LMS Users - India'])) {
                    $assignedTagIds[] = $tags['Koha LMS Users - India']->id;
                }
                if (isset($tags['IIT & NIT Institutional Contacts'])) {
                    $assignedTagIds[] = $tags['IIT & NIT Institutional Contacts']->id;
                }
            } elseif (str_contains($company, 'razorpay') || str_contains($company, 'zerodha') || str_contains($company, 'freshworks') || str_contains($company, 'postman') || str_contains($company, 'swiggy') || str_contains($company, 'zomato') || str_contains($company, 'cred') || str_contains($company, 'groww') || str_contains($company, 'phonepe')) {
                if (isset($tags['SaaS & Startup Founders'])) {
                    $assignedTagIds[] = $tags['SaaS & Startup Founders']->id;
                }
                if (isset($tags['CTO & Tech Leaders - India'])) {
                    $assignedTagIds[] = $tags['CTO & Tech Leaders - India']->id;
                }
                if (isset($tags['Fintech Product Managers'])) {
                    $assignedTagIds[] = $tags['Fintech Product Managers']->id;
                }
            } elseif (str_contains($company, 'tcs') || str_contains($company, 'infosys') || str_contains($company, 'wipro') || str_contains($company, 'hcl') || str_contains($company, 'tata') || str_contains($company, 'ltimindtree') || str_contains($company, 'larsentoubro') || str_contains($company, 'reliance')) {
                if (isset($tags['IT & Infrastructure Heads - Mumbai/BLR'])) {
                    $assignedTagIds[] = $tags['IT & Infrastructure Heads - Mumbai/BLR']->id;
                }
                if (isset($tags['Enterprise Procurement'])) {
                    $assignedTagIds[] = $tags['Enterprise Procurement']->id;
                }
            } else {
                if (isset($tags['Marketing & Growth Leads'])) {
                    $assignedTagIds[] = $tags['Marketing & Growth Leads']->id;
                }
                if (isset($tags['HR & People Operations'])) {
                    $assignedTagIds[] = $tags['HR & People Operations']->id;
                }
            }

            // Ensure every contact has at least 1-2 tags
            if (empty($assignedTagIds)) {
                $randomTags = $tags->random(min(2, $tags->count()))->pluck('id')->toArray();
                $assignedTagIds = $randomTags;
            }

            foreach ($assignedTagIds as $tagId) {
                ContactTag::firstOrCreate([
                    'contact_id' => $contact->id,
                    'tag_id' => $tagId,
                ]);
            }
        }
    }
}
