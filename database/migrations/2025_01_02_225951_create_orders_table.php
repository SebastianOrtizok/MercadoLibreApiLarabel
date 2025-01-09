<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // Clave primaria
            $table->unsignedBigInteger('user_id'); // Clave foránea a usuarios
            $table->string('status'); // Estado del pedido
            $table->decimal('total', 10, 2); // Total del pedido
            $table->timestamps(); // created_at y updated_at

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Restricción de clave foránea
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
