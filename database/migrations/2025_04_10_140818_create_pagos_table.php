<?php

// database/migrations/XXXX_XX_XX_create_pagos_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosTable extends Migration
{
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('suscripcion_id')->constrained('suscripciones')->onDelete('cascade');
            $table->decimal('monto', 10, 2); // Monto pagado
            $table->string('metodo_pago')->nullable(); // 'mercadopago', 'stripe', etc.
            $table->string('id_transaccion')->nullable(); // ID de la transacción en la pasarela
            $table->string('estado')->default('pendiente'); // 'pendiente', 'completado', 'fallido'
            $table->timestamp('fecha_pago')->useCurrent(); // Fecha del pago
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagos');
    }
}
