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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('set null'); // 'client_id' -> 'cliente_id' y referencia a 'clientes'
            $table->foreignId('users_id')->nullable()->constrained('users')->onDelete('set null'); // Añadido para el usuario que realiza la venta
            $table->decimal('monto_total', 10, 2); // 'total_amount' -> 'monto_total'
            $table->decimal('monto_recibido', 10, 2)->default(0); // 'received_amount' -> 'monto_recibido'
            $table->decimal('cambio', 10, 2)->default(0); // 'change_amount' -> 'cambio'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};