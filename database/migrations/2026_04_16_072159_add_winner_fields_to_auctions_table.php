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
            $table->foreignId('winner_user_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->foreignId('winning_bid_id')->nullable()->after('winner_user_id')->constrained('bids')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->dropForeign(['winner_user_id']);
            $table->dropForeign(['winning_bid_id']);
            $table->dropColumn(['winner_user_id', 'winning_bid_id']);
        });
    }
};
