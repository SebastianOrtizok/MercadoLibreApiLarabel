<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoriasToItemsCompetidoresTable extends Migration
{
    public function up()
    {
        Schema::table('items_competidores', function (Blueprint $table) {
            $table->text('categorias')->nullable()->after('url');
        });
    }

    public function down()
    {
        Schema::table('items_competidores', function (Blueprint $table) {
            $table->dropColumn('categorias');
        });
    }
}
