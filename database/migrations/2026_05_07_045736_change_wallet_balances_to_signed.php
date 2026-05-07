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
        Schema::table('wallets', function (Blueprint $table) {
            $table->bigInteger('balance_yen')->default(0)->change();
            $table->bigInteger('locked_balance_yen')->default(0)->change();
            $table->bigInteger('withdrawal_locked_yen')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->unsignedBigInteger('balance_yen')->default(0)->change();
            $table->unsignedBigInteger('locked_balance_yen')->default(0)->change();
            $table->unsignedBigInteger('withdrawal_locked_yen')->default(0)->change();
        });
    }
};
