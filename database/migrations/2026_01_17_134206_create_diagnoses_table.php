<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('diagnoses', function (Blueprint $table) {
            $table->id();

            // patient (optional relation) => users table (role = patient)
            $table->foreignId('patient_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // saved snapshot name (حتى لو patient_id بقى null بعد حذف اليوزر)
            $table->string('patient_name')->nullable();

            // diagnosis data
            $table->string('public_diagnosis');
            $table->string('private_diagnosis')->nullable();

            // meta
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnoses');
    }
};
