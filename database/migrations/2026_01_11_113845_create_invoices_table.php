<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // invoice meta
            $table->string('invoice_no')->unique(); // INV-2023-00892
            $table->enum('type', ['sale', 'purchase'])->default('sale'); // Sales / Purchases

            $table->dateTime('issued_at')->nullable();

            // client (patient/user) optional
            $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('client_name')->nullable(); // fallback لو مش موجود user

            $table->enum('payment_method', ['cash', 'card', 'wallet', 'insurance'])->default('cash');
            $table->enum('status', ['paid', 'pending', 'partially_paid'])->default('paid');

            // totals
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);

            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('balance_due', 10, 2)->default(0);

            // who created
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
