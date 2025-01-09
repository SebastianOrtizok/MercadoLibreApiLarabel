<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id(); // Clave primaria
            $table->unsignedBigInteger('inventory_id'); // Clave foránea
            $table->foreign('inventory_id')->references('id')->on('inventories')->onDelete('cascade');
            $table->string('type'); // Entrada, salida, traslado, etc.
            $table->integer('quantity');
            $table->string('status');
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
        Schema::dropIfExists('transactions');
    }
}
