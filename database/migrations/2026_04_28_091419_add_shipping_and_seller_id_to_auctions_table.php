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
            $table->integer('shipping_fee_yen')->nullable()->after('current_bid_yen');
            $table->string('yahoo_seller_id')->nullable()->after('seller_name');
            $table->index('yahoo_seller_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->dropColumn(['shipping_fee_yen', 'yahoo_seller_id']);
        });
    }
};
