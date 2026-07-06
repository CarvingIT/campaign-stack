<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sent_mails', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('newsletter_id');
            $table->unsignedBigInteger('outbound_mail_account_id');
            $table->unsignedBigInteger('contact_id');
            $table->string('subject');
            $table->text('body');
            $table->tinyInteger('opened')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sent_mails');
    }
};
