<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id(); // Clave primaria
            $table->unsignedBigInteger('order_id'); // Clave foránea a orders
            $table->string('product_name'); // Nombre del producto
            $table->unsignedBigInteger('sku_id'); // SKU relacionado
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps(); // created_at y updated_at

            // Clave foránea para Orden y SKU relacionados
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('sku_id')->references('id')->on('skus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
