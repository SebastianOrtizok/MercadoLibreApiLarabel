<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemPromotionsTable extends Migration
{
    public function up()
    {
        Schema::create('item_promotions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ml_product_id', 255); // Vincula con articulos
            $table->string('promotion_id', 20);   // ID de la promoción
            $table->string('type', 50)->nullable(); // Tipo de promoción
            $table->string('status', 20)->nullable(); // Estado
            $table->decimal('original_price', 10, 2)->nullable(); // Precio original de la promo
            $table->decimal('new_price', 10, 2)->nullable();      // Precio con descuento
            $table->dateTime('start_date')->nullable(); // Inicio
            $table->dateTime('finish_date')->nullable(); // Fin
            $table->string('name', 255)->nullable(); // Nombre
            $table->timestamps();

            // Índices
            $table->unique(['ml_product_id', 'promotion_id'], 'promo_item_unique');
            $table->index('ml_product_id');
            $table->index('finish_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_promotions');
    }
}
