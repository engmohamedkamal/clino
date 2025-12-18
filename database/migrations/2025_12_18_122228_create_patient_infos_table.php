<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patient_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('gender')->nullable();               // male / female
            $table->date('dob')->nullable();                   // تاريخ الميلاد
            $table->string('phone')->nullable();               // رقم الهاتف
            $table->string('address')->nullable();             // العنوان
            $table->string('blood_type')->nullable();          // فصيلة الدم
            $table->float('weight')->nullable();              // الوزن
            $table->float('height')->nullable();              // الطول

            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();

            $table->text('medical_history')->nullable();      // تاريخ مرضي سابق
            $table->text('allergies')->nullable();            // الحساسية
            $table->text('current_medications')->nullable();  // أدوية حالية
            $table->text('notes')->nullable();                // ملاحظات إضافية

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_infos');
    }
};
