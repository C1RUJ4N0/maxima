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
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade'); // 'client_id' -> 'cliente_id' y referencia a 'clientes'
            $table->decimal('monto_total', 10, 2); // 'total_amount' -> 'monto_total'
            $table->decimal('monto_pagado', 10, 2)->default(0); // 'amount_paid' -> 'monto_pagado'
            $table->date('fecha_vencimiento'); // 'due_date' -> 'fecha_vencimiento'
            $table->string('estado')->default('vigente'); // 'status' -> 'estado'
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
