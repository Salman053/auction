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
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->string('yahoo_auction_id')->unique();
            $table->string('title');
            $table->string('condition')->nullable();
            $table->unsignedBigInteger('starting_bid_yen')->default(0);
            $table->unsignedBigInteger('current_bid_yen')->default(0);
            $table->unsignedInteger('bid_count')->default(0);
            $table->string('status')->index();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable()->index();
            $table->string('seller_name')->nullable();
            $table->decimal('seller_rating', 5, 2)->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->json('image_urls')->nullable();
            $table->json('raw')->nullable();
            $table->timestamp('last_synced_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};
