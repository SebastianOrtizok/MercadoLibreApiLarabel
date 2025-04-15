<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsCompetidoresTable extends Migration
{
    public function up()
    {
        Schema::create('items_competidores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('competidor_id');
            $table->string('item_id');
            $table->string('titulo');
            $table->decimal('precio', 10, 2);
            $table->integer('cantidad_disponible')->default(0);
            $table->integer('cantidad_vendida')->default(0);
            $table->boolean('envio_gratis')->default(false);
            $table->timestamp('ultima_actualizacion')->nullable();
            $table->timestamps();

            $table->foreign('competidor_id')->references('id')->on('competidores')->onDelete('cascade');
            $table->index('item_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('items_competidores');
    }
}
