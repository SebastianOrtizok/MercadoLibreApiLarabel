<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSkuInternoToArticulos extends Migration
{
    public function up()
    {
        Schema::table('articulos', function (Blueprint $table) {
            $table->string('sku_interno', 255)->nullable()->after('sku');
        });
    }

    public function down()
    {
        Schema::table('articulos', function (Blueprint $table) {
            $table->dropColumn('sku_interno');
        });
    }
}
