<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id(); // Clave primaria
            $table->string('name'); // Nombre del inventario
            $table->text('description')->nullable(); // Descripción del inventario
            $table->integer('stock')->default(0); // Cantidad en stock
            $table->decimal('price', 10, 2)->nullable(); // Precio por unidad
            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventories');
    }
}
