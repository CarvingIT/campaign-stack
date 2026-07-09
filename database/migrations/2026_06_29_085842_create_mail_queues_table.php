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
        Schema::create('mail_queues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('newsletter_id');
            $table->unsignedBigInteger('contact_id');
            $table->string('subject');
            $table->text('body');
            $table->char('status', 1)->nullable();
            $table->tinyInteger('attempt')->default(0);
            $table->dateTime('sending_attempted_at')->nullable();
            $table->integer('response_code')->nullable();
            $table->string('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_queues');
    }
};
