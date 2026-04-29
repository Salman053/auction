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
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->string('receipt_path')->nullable()->after('memo');
        });

        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->string('receipt_path')->nullable()->after('memo');
            $table->string('transaction_id')->nullable()->after('receipt_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropColumn('receipt_path');
        });

        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->dropColumn(['receipt_path', 'transaction_id']);
        });
    }
};
