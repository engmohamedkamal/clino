<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Basic info
            $table->string('name');
            $table->string('sku')->unique(); // ✅ SKU unique
            $table->string('category')->nullable();
            $table->string('unit')->nullable(); // pcs, box, bottle...

            // Pricing
            $table->decimal('purchase_price', 10, 2)->default(0);
            $table->decimal('selling_price', 10, 2)->default(0);

            // Stock
            $table->integer('quantity')->default(0);
            $table->integer('reorder_level')->default(0);

            // Extra
            $table->string('location')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('supplier')->nullable();

            // Status
            $table->boolean('status')->default(true);

            // Image
            $table->string('image')->nullable();

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
