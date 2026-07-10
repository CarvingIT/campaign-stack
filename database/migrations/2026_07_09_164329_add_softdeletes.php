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
        Schema::table('outbound_mail_accounts', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('tags', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('newsletters', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('campaigns', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outbound_mail_accounts', function (Blueprint $table) {
            //
        });
    }
};
