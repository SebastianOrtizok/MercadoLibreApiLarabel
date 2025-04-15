<?php
// database/migrations/XXXX_XX_XX_create_suscripciones_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuscripcionesTable extends Migration
{
    public function up()
    {
        Schema::create('suscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade'); // Relación con tabla users
            $table->string('plan')->nullable(); // 'mensual', 'trimestral', 'anual'
            $table->decimal('monto', 10, 2); // Monto del plan
            $table->timestamp('fecha_inicio')->useCurrent(); // Fecha de inicio
            $table->timestamp('fecha_fin')->nullable(); // Fecha de vencimiento
            $table->string('estado')->default('activo'); // 'activo', 'vencido', 'cancelado'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('suscripciones');
    }
}
