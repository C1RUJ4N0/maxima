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
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('set null');
            $table->foreignId('users_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('monto_total', 10, 2);
            $table->decimal('monto_recibido', 10, 2)->default(0);
            $table->decimal('cambio', 10, 2)->default(0);
            $table->string('metodo_pago')->default('efectivo'); // <-- LÍNEA AÑADIDA
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