<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_invoice_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_invoice_id')
                ->constrained('service_invoices')
                ->cascadeOnDelete();

            // لو عندك جدول services:
            $table->foreignId('service_id')
                ->nullable()
                ->constrained('services')
                ->nullOnDelete();

            // Snapshot
            $table->string('service_name');      // حتى لو service_id null
            $table->string('doctor_name')->nullable();
            $table->string('department')->nullable();

            $table->unsignedInteger('qty')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_invoice_items');
    }
};
