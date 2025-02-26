<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsToArticulosTable extends Migration
{
    public function up()
    {
        Schema::table('articulos', function (Blueprint $table) {
            // Campos adicionales que propusiste
            $table->string('logistic_type')->nullable()->after('ml_product_id');
            $table->string('inventory_id')->nullable()->after('logistic_type');
            $table->string('user_product_id')->nullable()->after('inventory_id');
            $table->decimal('precio_original', 10, 2)->nullable()->after('precio');
            $table->string('category_id', 255)->nullable()->after('en_catalogo');

            // Campos para promociones
            $table->boolean('en_promocion')->default(false)->after('precio_original');
            $table->float('descuento_porcentaje')->nullable()->after('en_promocion');
            $table->json('deal_ids')->nullable()->after('descuento_porcentaje');
        });
    }

    public function down()
    {
        Schema::table('articulos', function (Blueprint $table) {
            $table->dropColumn([
                'logistic_type', 'inventory_id', 'user_product_id', 'precio_original',
                'category_id', 'en_promocion', 'descuento_porcentaje', 'deal_ids'
            ]);
        });
    }
}
