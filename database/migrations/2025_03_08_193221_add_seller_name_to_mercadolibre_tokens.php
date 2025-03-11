<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSellerNameToMercadolibreTokens extends Migration
{
    public function up()
    {
        Schema::table('mercadolibre_tokens', function (Blueprint $table) {
            $table->string('seller_name', 255)->nullable()->after('ml_account_id');
        });
    }

    public function down()
    {
        Schema::table('mercadolibre_tokens', function (Blueprint $table) {
            $table->dropColumn('seller_name');
        });
    }
}
