<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mail_queues', function (Blueprint $table) {
            $table->unsignedBigInteger('outbound_mail_account_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('mail_queues', function (Blueprint $table) {
            $table->dropColumn('outbound_mail_account_id');
        });
    }
};