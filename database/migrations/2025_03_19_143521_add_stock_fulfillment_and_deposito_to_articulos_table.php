<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStockFulfillmentAndDepositoToArticulosTable extends Migration
{
    public function up()
    {
        Schema::table('articulos', function (Blueprint $table) {
            $table->integer('stock_fulfillment')->default(0)->after('stock_actual');
            $table->integer('stock_deposito')->default(0)->after('stock_fulfillment');
        });
    }

    public function down()
    {
        Schema::table('articulos', function (Blueprint $table) {
            $table->dropColumn(['stock_fulfillment', 'stock_deposito']);
        });
    }
}
