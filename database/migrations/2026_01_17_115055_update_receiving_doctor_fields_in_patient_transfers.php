<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('patient_transfers', function (Blueprint $table) {
      // ✅ أضف receiving_doctor_name لو مش موجود
      if (!Schema::hasColumn('patient_transfers', 'receiving_doctor_name')) {
        $table->string('receiving_doctor_name')->nullable()->after('primary_physician_id');
      }

      // ✅ احذف receiving_doctor_id لو مش هتستخدمه
      if (Schema::hasColumn('patient_transfers', 'receiving_doctor_id')) {
        $table->dropConstrainedForeignId('receiving_doctor_id');
      }

      // ✅ أضف attachments json
      if (!Schema::hasColumn('patient_transfers', 'attachments')) {
        $table->json('attachments')->nullable()->after('receiving_phone');
      }
    });
  }

  public function down(): void
  {
    Schema::table('patient_transfers', function (Blueprint $table) {
      if (Schema::hasColumn('patient_transfers', 'attachments')) {
        $table->dropColumn('attachments');
      }

      if (Schema::hasColumn('patient_transfers', 'receiving_doctor_name')) {
        $table->dropColumn('receiving_doctor_name');
      }

      // رجّع receiving_doctor_id لو محتاج
      $table->foreignId('receiving_doctor_id')->nullable()->constrained('users')->nullOnDelete();
    });
  }
};
