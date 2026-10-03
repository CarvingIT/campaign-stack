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
        Schema::table('mail_queues', function (Blueprint $table) {
            $table->unsignedBigInteger('newsletter_id')->nullable()->change();
            if (!Schema::hasColumn('mail_queues', 'outbound_mail_account_id')) {
                $table->unsignedBigInteger('outbound_mail_account_id')->nullable()->after('newsletter_id');
            }
        });

        Schema::table('sent_mails', function (Blueprint $table) {
            $table->unsignedBigInteger('newsletter_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mail_queues', function (Blueprint $table) {
            $table->unsignedBigInteger('newsletter_id')->nullable(false)->change();
            if (Schema::hasColumn('mail_queues', 'outbound_mail_account_id')) {
                $table->dropColumn('outbound_mail_account_id');
            }
        });

        Schema::table('sent_mails', function (Blueprint $table) {
            $table->unsignedBigInteger('newsletter_id')->nullable(false)->change();
        });
    }
};
