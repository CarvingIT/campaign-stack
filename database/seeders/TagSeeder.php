<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tag::create(
            [
                'label'=>'Librarian'
            ]
        );
        Tag::create(
            [
                'label'=>'Koha user'
            ]
        );
        Tag::create(
            [
                'label'=>'Client'
            ]
        );
        Tag::create(
            [
                'label'=>'CITPL'
            ]
        );
        Tag::create(
            [
                'label'=>'Ex CITPL'
            ]
        );
    }
}
