<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id(); // Clave primaria
            $table->unsignedBigInteger('inventory_id'); // Inventario relacionado
            $table->string('type'); // Ej. inventario general, faltante, sobrante, etc.
            $table->decimal('value', 10, 2);
            $table->timestamps(); // created_at y updated_at

            // Clave foránea para Inventario relacionado
            $table->foreign('inventory_id')->references('id')->on('inventories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
