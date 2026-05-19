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
        Schema::table('auctions', function (Blueprint $table) {
            $table->index('current_bid_yen');
            $table->index('bid_count');
            $table->index('created_at');
            $table->boolean('has_images')->storedAs('image_urls IS NOT NULL')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->dropIndex(['current_bid_yen']);
            $table->dropIndex(['bid_count']);
            $table->dropIndex(['created_at']);
            $table->dropColumn('has_images');
        });
    }
};
