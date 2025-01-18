<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
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
            $table->string('permalink', 500)->collation('utf8mb4_unicode_ci')->default('#');
            $table->string('condicion')->default('Desconocido');
            $table->string('sku')->nullable();
            $table->string('tipo_publicacion')->nullable();
            $table->boolean('en_catalogo')->nullable();
            $table->timestamps();

            // Establecer el conjunto de caracteres para la tabla si es necesario
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articulos');
    }
}
