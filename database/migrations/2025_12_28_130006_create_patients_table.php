<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            $table->string('patient_name');
            $table->string('patient_number')->nullable();
            $table->date('dob')->nullable();

            $table->string('patient_email')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();

            $table->string('id_number')->nullable();
            $table->string('address')->nullable();

            $table->text('about')->nullable();

            $table->timestamps();

            // اختياري (لو عايز تمنع تكرار نفس رقم الموبايل)
            // $table->unique('patient_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
