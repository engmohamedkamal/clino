<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_invoices', function (Blueprint $table) {
            $table->id();

            // Invoice Number
            $table->string('invoice_no')->unique(); // مثال: INV-2026-000001

            // Patient (User role=patient)
            $table->foreignId('patient_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Snapshot fields (حتى لو المريض اتعدل)
            $table->string('patient_name')->nullable();
            $table->string('patient_phone')->nullable();
            $table->string('patient_code')->nullable(); // PID-88392 لو عندك

            // Optional insurance
            $table->string('insurance_provider')->nullable();

            // Notes
            $table->text('notes')->nullable();

            // Payment
            $table->enum('payment_method', ['card', 'cash', 'insurance', 'wallet'])->default('cash');
            $table->enum('payment_status', ['pending', 'paid', 'partial'])->default('pending');

            // Money
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0); // مثال 10.00
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            // Meta
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_invoices');
    }
};
