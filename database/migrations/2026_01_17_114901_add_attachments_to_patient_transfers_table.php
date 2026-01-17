<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('patient_transfers', function (Blueprint $table) {
      $table->json('attachments')->nullable()->after('receiving_phone');
      // لو MySQL قديم ومش بيدعم JSON:
      // $table->text('attachments')->nullable()->after('receiving_phone');
    });
  }

  public function down(): void
  {
    Schema::table('patient_transfers', function (Blueprint $table) {
      $table->dropColumn('attachments');
    });
  }
};
