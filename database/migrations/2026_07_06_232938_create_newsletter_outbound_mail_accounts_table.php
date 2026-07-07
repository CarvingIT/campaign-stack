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
        Schema::create('newsletter_outbound_mail_accounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('newsletter_id');
            $table->bigInteger('outbound_mail_account_id');
            $table->tinyInteger('priority');
            $table->timestamps();
    
            $table->unique(['newsletter_id', 'outbound_mail_account_id'], 'newsletter_mailaccount_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_outbound_mail_accounts');
    }
};
