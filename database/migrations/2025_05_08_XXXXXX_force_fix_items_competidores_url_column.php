<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Acortar URLs existentes para evitar conflictos
        DB::statement("UPDATE items_competidores SET url = SUBSTRING(url, 1, 255) WHERE LENGTH(url) > 255");

        // Cambiar 'url' a TEXT
        DB::statement('ALTER TABLE items_competidores ALTER COLUMN url TYPE TEXT');

        // Asegurarse de que 'precio_descuento' sea DECIMAL(10,2)
        DB::statement('ALTER TABLE items_competidores ALTER COLUMN precio_descuento TYPE NUMERIC(10,2)');

        // Confirmar que 'es_full' sea BOOLEAN
        DB::statement('ALTER TABLE items_competidores ALTER COLUMN es_full TYPE BOOLEAN USING (es_full::BOOLEAN)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir 'url' a VARCHAR(255)
        DB::statement('ALTER TABLE items_competidores ALTER COLUMN url TYPE VARCHAR(255)');

        // Revertir 'precio_descuento' a DECIMAL(12,2)
        DB::statement('ALTER TABLE items_competidores ALTER COLUMN precio_descuento TYPE NUMERIC(12,2)');

        // Revertir 'es_full' a BOOLEAN (ya es BOOLEAN, pero lo dejamos por consistencia)
        DB::statement('ALTER TABLE items_competidores ALTER COLUMN es_full TYPE BOOLEAN USING (es_full::BOOLEAN)');
    }
};
