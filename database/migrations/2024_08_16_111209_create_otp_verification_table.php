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
        Schema::create('otp_verification', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('otp_code', 4)->nullable();
            $table->tinyInteger('otp_type')->default(1)->comment('1=send_otp, 2=resend_otp')->nullable();
            $table->tinyInteger('is_verified')->default(0)->comment('0=not_verified, 1=verified, 2=delete')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->bigInteger('user_id')->comment('FOREIGN KEYs')->nullable();
            $table->string('mobile_number', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_verification');
    }
};
