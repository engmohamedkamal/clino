<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();

            // ✅ Product
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();

            // Snapshot data (حتى لو المنتج اتغير بعدين تفضل الفاتورة ثابتة)
            $table->string('item_name');     // اسم المنتج وقت الفاتورة
            $table->string('sku')->nullable();

            $table->decimal('unit_price', 10, 2)->default(0);
            $table->integer('qty')->default(1);
            $table->decimal('line_total', 10, 2)->default(0);

            $table->integer('stock_before')->nullable();
            $table->integer('stock_after')->nullable();

            $table->timestamps();

            $table->index(['invoice_id']);
            $table->index(['product_id']);
            $table->index(['sku']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
