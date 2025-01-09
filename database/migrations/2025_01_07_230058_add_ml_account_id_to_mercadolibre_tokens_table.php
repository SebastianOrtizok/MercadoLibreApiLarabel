<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMlAccountIdToMercadolibreTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_tokens', function (Blueprint $table) {
            // Añadir columna ml_account_id
            $table->string('ml_account_id')->after('user_id');

            // Crear índice único para evitar duplicados
            $table->unique(['user_id', 'ml_account_id'], 'user_account_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_tokens', function (Blueprint $table) {
            // Eliminar el índice único
            $table->dropUnique('user_account_unique');

            // Eliminar la columna ml_account_id
            $table->dropColumn('ml_account_id');
        });
    }
}
