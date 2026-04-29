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
        Schema::create('scraping_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('run_uuid')->index();
            $table->foreignId('proxy_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->index();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('auctions_created')->default(0);
            $table->unsignedInteger('auctions_updated')->default(0);
            $table->unsignedInteger('auctions_closed')->default(0);
            $table->unsignedInteger('auctions_failed')->default(0);
            $table->text('error_message')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scraping_logs');
    }
};
