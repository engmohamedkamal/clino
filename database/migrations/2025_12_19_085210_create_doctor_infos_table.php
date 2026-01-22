<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_infos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete()
                ->unique();

            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('dob')->nullable();

        
            $table->json('Specialization')->nullable();        
            $table->json('availability_schedule')->nullable();  

            $table->string('license_number')->nullable();
            $table->string('address')->nullable();

            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('social_link');

            $table->json('activities')->nullable();
            $table->json('skills')->nullable();
            $table->json('visit_types')->nullable(); 
            $table->string('image')->nullable();
            $table->text('about')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_infos');
    }
};
