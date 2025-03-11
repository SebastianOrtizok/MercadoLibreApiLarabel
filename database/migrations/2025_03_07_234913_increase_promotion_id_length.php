<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncreasePromotionIdLength extends Migration
{
    public function up()
    {
        Schema::table('item_promotions', function (Blueprint $table) {
            $table->string('promotion_id', 50)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('item_promotions', function (Blueprint $table) {
            $table->string('promotion_id', 32)->nullable()->change();
        });
    }
}
