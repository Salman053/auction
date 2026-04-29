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
        Schema::create('proxies', function (Blueprint $table) {
            $table->id();
            $table->string('scheme')->default('http');
            $table->string('host')->index();
            $table->unsignedInteger('port');
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('country')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('success_count')->default(0);
            $table->unsignedInteger('failure_count')->default(0);
            $table->unsignedInteger('avg_response_ms')->nullable();
            $table->timestamp('last_used_at')->nullable()->index();
            $table->timestamp('last_checked_at')->nullable()->index();
            $table->timestamp('disabled_until')->nullable()->index();
            $table->text('last_error')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['scheme', 'host', 'port', 'username']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proxies');
    }
};
