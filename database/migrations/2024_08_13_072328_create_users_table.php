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
            $table->id();
            $table->string('full_name')->nullable();
            $table->string('sur_name')->nullable();
            $table->string('unique_id')->nullable();
            $table->timestamp('birth_date')->nullable();
            $table->string('gender', 1)->comment('M=male, F=female, O=others')->nullable();
            $table->string('mobile_number', 20)->unique()->nullable();
            $table->string('email_address')->unique()->nullable();
            $table->string('password')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('profile_picture')->nullable();
            $table->tinyInteger('is_agree_terms')->default(1)->comment('0=disagree, 1=agree')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0=delete, 1=active, 2=inactive')->nullable();
            $table->string('fcm_token')->nullable();
            $table->timestamp('deactivate_at')->nullable();
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
