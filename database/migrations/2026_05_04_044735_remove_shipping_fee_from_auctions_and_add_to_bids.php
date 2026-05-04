<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->dropColumn('shipping_fee_yen');
        });

        Schema::table('bids', function (Blueprint $table) {
            $table->foreignId('shipping_rate_id')->nullable()->constrained('shipping_rates')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bids', function (Blueprint $table) {
            $table->dropForeign(['shipping_rate_id']);
            $table->dropColumn('shipping_rate_id');
        });

        Schema::table('auctions', function (Blueprint $table) {
            $table->integer('shipping_fee_yen')->nullable();
        });
    }
};
