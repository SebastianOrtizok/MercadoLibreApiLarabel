<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFulfillmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fulfillment', function (Blueprint $table) {
            $table->id(); // Clave primaria
            $table->unsignedBigInteger('order_item_id'); // Relación con Order Items
            $table->string('status');
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps(); // created_at y updated_at

            // Clave foránea para Order Item relacionado
            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fulfillment');
    }
}
