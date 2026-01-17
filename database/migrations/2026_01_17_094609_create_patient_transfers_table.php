<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('patient_transfers', function (Blueprint $table) {
      $table->id();

      // روابط أساسية
      $table->string('patient_name');
      $table->foreignId('primary_physician_id')->nullable()->constrained('users')->nullOnDelete(); // دكتور/يوزر
      $table->foreignId('receiving_doctor_id')->nullable()->constrained('users')->nullOnDelete();  // دكتور مستلم

      // بيانات ظاهرة في الهيدر
      $table->string('transfer_code')->nullable();        // #44521
      $table->string('transfer_priority')->default('urgent'); // urgent/normal
      $table->unsignedSmallInteger('age')->nullable();
      $table->string('gender', 20)->nullable();           // Male/Female
      $table->string('blood_type', 10)->nullable();       // O+
      $table->string('current_location')->nullable();     // Cardiology, Ward 4B

      // Clinical Assessment
      $table->string('reason_for_transfer')->nullable();  
      $table->string('stability_status')->default('stable'); // stable/guarded/critical
      $table->string('primary_diagnosis')->nullable();
      $table->text('medical_summary')->nullable();

      // Transport & Logistics
      $table->string('transport_mode')->nullable();       // als_ambulance / wheelchair_van / ...
      $table->boolean('continuous_oxygen')->default(false);
      $table->boolean('cardiac_monitoring')->default(false);

      // Destination
      $table->string('destination_hospital')->nullable();  // El Shamla Hospital
      $table->string('destination_dept_unit')->nullable(); // ICU - Tower B
      $table->string('destination_bed_no')->nullable();    // BED-402-A2
      $table->string('receiving_phone')->nullable();       // 012...

      // Status / Workflow
      $table->string('bed_status')->default('pending');    // pending/confirmed/denied
      $table->string('status')->default('draft');          // draft/submitted/in_transit/completed/cancelled
      $table->timestamp('submitted_at')->nullable();

      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('patient_transfers');
  }
};
