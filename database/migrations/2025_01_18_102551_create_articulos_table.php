<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticulosTable extends Migration
{
    public function up()
    {
        Schema::create('articulos', function (Blueprint $table) {
            $table->id(); // ID de la tabla
            $table->unsignedBigInteger('user_id'); // Almacena el ml_account_id directamente
            $table->string('ml_product_id')->nullable(); // ID de MercadoLibre
            $table->string('titulo')->default('Sin título');
            $table->string('imagen')->nullable();
            $table->integer('stock_actual')->default(0);
            $table->decimal('precio', 10, 2)->nullable();
            $table->string('estado')->default('Desconocido');
            $table->string('permalink', 500)->default('#'); // Eliminamos ->collation()
            $table->string('condicion')->default('Desconocido');
            $table->string('sku')->nullable();
            $table->string('tipo_publicacion')->nullable();
            $table->boolean('en_catalogo')->nullable();
            $table->timestamps();
            // Eliminamos $table->charset y $table->collation
        });
    }

    public function down()
    {
        Schema::dropIfExists('articulos');
    }
}
