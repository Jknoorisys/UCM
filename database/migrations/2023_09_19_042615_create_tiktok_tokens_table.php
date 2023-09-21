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
        Schema::create('tiktok_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->text('advertiser_id')->unique();
            $table->text('auth_url')->unique();
            $table->text('app_id')->unique();
            $table->text('secret')->unique();
            $table->text('auth_code')->unique();
            $table->text('access_token')->unique();
            $table->enum('status', ['unlinked', 'inprogress', 'linked'])->default('unlinked');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiktok_tokens');
    }
};
