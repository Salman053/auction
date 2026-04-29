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
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Country or Port name
            $table->integer('fee_yen');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('shipping_rate_id')->nullable()->constrained('shipping_rates');
        });

        Schema::table('bids', function (Blueprint $table) {
            $table->timestamp('canceled_at')->nullable();
        });

        Schema::table('auctions', function (Blueprint $table) {
            $table->string('shipment_status')->default('pending'); // pending, bidder_confirmed, admin_approved
            $table->timestamp('bidder_confirmed_at')->nullable();
            $table->timestamp('admin_approved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->dropColumn(['shipment_status', 'bidder_confirmed_at', 'admin_approved_at']);
        });

        Schema::table('bids', function (Blueprint $table) {
            $table->dropColumn('canceled_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipping_rate_id');
        });

        Schema::dropIfExists('shipping_rates');
    }
};
