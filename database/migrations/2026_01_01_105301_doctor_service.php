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
        Schema::create('doctor_service', function (Blueprint $table) {
            $table->id();

            $table->foreignId('doctor_info_id')
                ->constrained('doctor_infos')
                ->cascadeOnDelete();

            $table->foreignId('service_id')
                ->constrained('services')
                ->cascadeOnDelete();

            // optional extras
            $table->decimal('price', 8, 2)->nullable();
            $table->integer('duration')->nullable(); // بالدقيقة
            $table->boolean('active')->default(true);

            $table->timestamps();

            // يمنع التكرار
            $table->unique(['doctor_info_id', 'service_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
