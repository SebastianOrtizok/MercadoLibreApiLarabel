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
        // Paso 1: Acortar URLs existentes a 255 caracteres para evitar conflictos
        DB::statement("UPDATE items_competidores SET url = SUBSTRING(url, 1, 255) WHERE LENGTH(url) > 255");

        // Paso 2: Forzar el cambio de 'url' a TEXT
        DB::statement('ALTER TABLE items_competidores MODIFY COLUMN url TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL');

        // Paso 3: Asegurarse de que 'precio_descuento' tenga el tipo correcto
        DB::statement('ALTER TABLE items_competidores MODIFY COLUMN precio_descuento DECIMAL(10,2) NULL DEFAULT NULL');

        // Paso 4: Asegurarse de que 'es_full' tenga el tipo correcto
        DB::statement('ALTER TABLE items_competidores MODIFY COLUMN es_full TINYINT(1) NOT NULL DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir 'url' a VARCHAR(255)
        DB::statement('ALTER TABLE items_competidores MODIFY COLUMN url VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL');

        // Revertir 'precio_descuento' a su estado anterior
        DB::statement('ALTER TABLE items_competidores MODIFY COLUMN precio_descuento DECIMAL(12,2) NULL DEFAULT NULL');

        // Revertir 'es_full' a su estado anterior
        DB::statement('ALTER TABLE items_competidores MODIFY COLUMN es_full TINYINT(1) NOT NULL DEFAULT 0');
    }
};
