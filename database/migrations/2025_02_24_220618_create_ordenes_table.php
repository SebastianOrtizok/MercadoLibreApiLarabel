<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenesTable extends Migration
{
    public function up()
    {
        Schema::create('ordenes', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID único de la orden');
            $table->unsignedBigInteger('ml_account_id')->comment('ID de la cuenta de MercadoLibre (seller_id, relacionado con articulos.user_id)');
            $table->string('ml_order_id', 255)->comment('ID único de la orden en MercadoLibre');
            $table->string('ml_product_id', 255)->comment('ID del producto en MercadoLibre (relacionado con articulos.ml_product_id)');
            $table->integer('cantidad')->default(1)->comment('Cantidad de unidades vendidas en esta orden');
            $table->decimal('precio_unitario', 10, 2)->comment('Precio unitario al momento de la venta');
            $table->string('estado_orden', 255)->default('unknown')->comment('Estado de la orden (e.g., paid, shipped)');
            $table->timestamp('fecha_venta')->comment('Fecha y hora de la venta');
            $table->timestamps();

            // Índices
            $table->index('ml_account_id', 'idx_ml_account_id');
            $table->index('ml_product_id', 'idx_ml_product_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ordenes');
    }
}
