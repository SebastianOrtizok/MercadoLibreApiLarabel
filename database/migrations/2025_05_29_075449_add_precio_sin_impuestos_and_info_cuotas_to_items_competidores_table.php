<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrecioSinImpuestosAndInfoCuotasToItemsCompetidoresTable extends Migration
{
    public function up()
    {
        Schema::table('items_competidores', function (Blueprint $table) {
            $table->decimal('precio_sin_impuestos', 10, 2)->nullable()->after('precio_descuento');
            $table->text('info_cuotas')->nullable()->after('precio_sin_impuestos');
        });
    }

    public function down()
    {
        Schema::table('items_competidores', function (Blueprint $table) {
            $table->dropColumn('precio_sin_impuestos');
            $table->dropColumn('info_cuotas');
        });
    }
}
