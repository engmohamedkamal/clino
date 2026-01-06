<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // ✅ خلي patient_id nullable
            $table->foreignId('patient_id')->nullable()->change();

            // ✅ لو لسه ما ضفتش patient_user_id
            if (!Schema::hasColumn('reports', 'patient_user_id')) {
                $table->foreignId('patient_user_id')
                    ->nullable()
                    ->after('patient_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // رجّعها NOT NULL (لو محتاج)
            $table->foreignId('patient_id')->nullable(false)->change();

            if (Schema::hasColumn('reports', 'patient_user_id')) {
                $table->dropConstrainedForeignId('patient_user_id');
            }
        });
    }
};
