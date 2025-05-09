<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Determinar si estamos en PostgreSQL o MySQL
        $connection = DB::getDriverName();

        // Asegurarse de que las columnas existan
        if (!Schema::hasColumn('items_competidores', 'precio_descuento')) {
            Schema::table('items_competidores', function (Blueprint $table) {
                $table->decimal('precio_descuento', 10, 2)->nullable()->after('precio');
            });
        } else {
            if ($connection === 'pgsql') {
                DB::statement('ALTER TABLE items_competidores ALTER COLUMN precio_descuento TYPE NUMERIC(10,2)');
            } else {
                DB::statement('ALTER TABLE items_competidores MODIFY COLUMN precio_descuento DECIMAL(10,2) NULL');
            }
        }

        if (!Schema::hasColumn('items_competidores', 'url')) {
            Schema::table('items_competidores', function (Blueprint $table) {
                $table->text('url')->nullable()->after('precio_descuento');
            });
        } else {
            if ($connection === 'pgsql') {
                DB::statement('ALTER TABLE items_competidores ALTER COLUMN url TYPE TEXT');
            } else {
                DB::statement('ALTER TABLE items_competidores MODIFY COLUMN url TEXT NULL');
            }
        }

        if (!Schema::hasColumn('items_competidores', 'es_full')) {
            Schema::table('items_competidores', function (Blueprint $table) {
                $table->boolean('es_full')->default(false)->after('url');
            });
        } else {
            if ($connection === 'pgsql') {
                DB::statement('ALTER TABLE items_competidores ALTER COLUMN es_full TYPE BOOLEAN USING (es_full::BOOLEAN)');
            } else {
                DB::statement('ALTER TABLE items_competidores MODIFY COLUMN es_full TINYINT(1) NOT NULL DEFAULT 0');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = DB::getDriverName();

        if (Schema::hasColumn('items_competidores', 'precio_descuento')) {
            if ($connection === 'pgsql') {
                DB::statement('ALTER TABLE items_competidores ALTER COLUMN precio_descuento TYPE NUMERIC(12,2)');
            } else {
                DB::statement('ALTER TABLE items_competidores MODIFY COLUMN precio_descuento DECIMAL(12,2) NULL');
            }
        }

        if (Schema::hasColumn('items_competidores', 'url')) {
            if ($connection === 'pgsql') {
                DB::statement('ALTER TABLE items_competidores ALTER COLUMN url TYPE VARCHAR(255)');
            } else {
                DB::statement('ALTER TABLE items_competidores MODIFY COLUMN url VARCHAR(255) NULL');
            }
        }

        if (Schema::hasColumn('items_competidores', 'es_full')) {
            if ($connection === 'pgsql') {
                DB::statement('ALTER TABLE items_competidores ALTER COLUMN es_full TYPE BOOLEAN USING (es_full::BOOLEAN)');
            } else {
                DB::statement('ALTER TABLE items_competidores MODIFY COLUMN es_full TINYINT(1) NOT NULL DEFAULT 0');
            }
        }
    }
};
