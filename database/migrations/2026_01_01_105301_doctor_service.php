<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doctor_service', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();

            $table->unsignedBigInteger('doctor_info_id');
            $table->unsignedBigInteger('service_id');

            $table->foreign('doctor_info_id')
                ->references('id')->on('doctor_infos')
                ->onDelete('cascade');

            $table->foreign('service_id')
                ->references('id')->on('services')
                ->onDelete('cascade');

            $table->decimal('price', 8, 2)->nullable();
            $table->integer('duration')->nullable();
            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->unique(['doctor_info_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_service');
    }
};
