<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFollowingToItemsCompetidores extends Migration
{
    public function up()
    {
        Schema::table('items_competidores', function (Blueprint $table) {
            $table->boolean('following')->default(false)->after('envio_gratis');
        });
    }

    public function down()
    {
        Schema::table('items_competidores', function (Blueprint $table) {
            $table->dropColumn('following');
        });
    }
}
