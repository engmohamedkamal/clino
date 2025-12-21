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
        Schema::create('doctor_infos', function (Blueprint $table) {
            $table->id();

            // علاقة مع جدول المستخدمين
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Gender (Male / Female / Other مثلاً)
            $table->string('gender')->nullable();

            // Specialization
            $table->string('specialization')->nullable();

            // License Number
            $table->string('license_number')->nullable();

            // Date of Birth
            $table->date('dob')->nullable();

            // Availability schedule
            $table->text('availability_schedule')->nullable();

            // Address
            $table->string('address')->nullable();

            // Profile picture (path)
            $table->string('image')->nullable();

            // Social URLs
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('twitter_url')->nullable();

            // About doctor
            $table->text('about')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_infos');
    }
};
