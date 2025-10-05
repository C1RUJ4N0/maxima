<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apartados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->decimal('monto_total', 10, 2);
            $table->decimal('monto_pagado', 10, 2)->default(0);
            $table->decimal('monto_restante', 10, 2)->default(0); // <-- LÍNEA AÑADIDA
            $table->date('fecha_vencimiento');
            $table->string('estado')->default('vigente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartados');
    }
};