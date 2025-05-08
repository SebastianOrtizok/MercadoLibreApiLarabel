<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Acortar URLs existentes a 255 caracteres para evitar problemas al cambiar el tipo
        DB::table('items_competidores')
            ->whereRaw('LENGTH(url) > 255')
            ->update([
                'url' => DB::raw('SUBSTRING(url, 1, 255)')
            ]);

        Schema::table('items_competidores', function (Blueprint $table) {
            // Cambiar 'url' a TEXT
            $table->text('url')->change();

            // Asegurarse de que 'precio_descuento' exista o modificarlo
            if (!Schema::hasColumn('items_competidores', 'precio_descuento')) {
                $table->decimal('precio_descuento', 10, 2)->nullable()->after('precio');
            } else {
                $table->decimal('precio_descuento', 10, 2)->nullable()->change();
            }

            // Asegurarse de que 'es_full' exista o modificarlo
            if (!Schema::hasColumn('items_competidores', 'es_full')) {
                $table->boolean('es_full')->default(false)->after('url');
            } else {
                $table->boolean('es_full')->default(false)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items_competidores', function (Blueprint $table) {
            // Revertir 'url' a VARCHAR(255)
            $table->string('url', 255)->change();

            // Si 'precio_descuento' no existía antes, eliminarlo
            if (!Schema::hasColumn('items_competidores', 'precio_descuento')) {
                $table->dropColumn('precio_descuento');
            } else {
                $table->decimal('precio_descuento', 10, 2)->nullable()->change();
            }

            // Si 'es_full' no existía antes, eliminarlo
            if (!Schema::hasColumn('items_competidores', 'es_full')) {
                $table->dropColumn('es_full');
            } else {
                $table->boolean('es_full')->default(false)->change();
            }
        });
    }
};
