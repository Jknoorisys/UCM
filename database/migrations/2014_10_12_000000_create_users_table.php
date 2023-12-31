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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('fname');
            $table->string('lname');
            $table->string('photo');
            $table->string('phone')->unique();
            $table->string('email')->unique();
            $table->integer('otp');
            $table->string('password');
            $table->text('JWT_token');
            $table->enum('is_social',['1','0'])->default('0');
            $table->enum('social_type',['google','facebook','apple','manual'])->default('manual');
            $table->string('social_id');
            $table->enum('is_verified', ['no', 'yes'])->default('no');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
