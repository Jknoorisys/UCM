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
        Schema::create('google_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('organization_id')->unique();
            $table->string('adaccount_id')->unique();
            $table->string('client_id')->unique();
            $table->string('client_secret')->unique();
            $table->string('auth_code')->unique();
            $table->text('access_token')->unique();
            $table->text('refresh_token')->unique();
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
        Schema::table('google_tokens', function (Blueprint $table) {
            $table->dropForeign('user_id');
        });
        Schema::dropIfExists('google_tokens');
        
    }
};
