<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('patient_transfers', function (Blueprint $table) {
      // اسم الدكتور المستلم (Text)
      $table->string('receiving_doctor_name')->nullable()->after('receiving_doctor_id');
    });
  }

  public function down(): void
  {
    Schema::table('patient_transfers', function (Blueprint $table) {
      $table->dropColumn('receiving_doctor_name');
    });
  }
};
