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
        Schema::create('admin_user', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('mobile_number', 20)->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('user_type')->default(1)->comment('1=admin, 2=staff, 3=field_officers')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
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
        Schema::dropIfExists('admin_user');
    }
};
