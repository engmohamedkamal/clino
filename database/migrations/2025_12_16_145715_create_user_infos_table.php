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
        Schema::create('user_infos', function (Blueprint $table) {
           $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('availability_schedule')->nullable();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('specialization')->nullable();
            $table->string('license_number')->nullable();
            $table->string('address')->nullable();
            $table->string('image')->nullable(); // path
            $table->text('about')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_infos');
    }
};
