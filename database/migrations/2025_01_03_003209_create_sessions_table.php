<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->id(); // Campo ID Primario
            $table->timestamp('created_at')->nullable(); // Campo para la fecha de creación
            $table->timestamp('updated_at')->nullable(); // Campo para la fecha de actualización
            $table->text('payload'); // Campo para los datos serializados de la sesión
            $table->integer('user_id')->nullable(); // ID del usuario asociado a la sesión
            $table->string('ip_address', 45)->nullable(); // Dirección IP asociada a la sesión
            $table->string('user_agent')->nullable(); // Información del agente del usuario
            $table->timestamp('last_activity')->nullable(); // Última actividad en la sesión
            $table->string('device_type')->nullable(); // Tipo de dispositivo (ej. móvil, escritorio, tablet)
            $table->string('platform')->nullable(); // Plataforma desde la que se accede (ej. Windows, iOS, Android)
            $table->string('browser')->nullable(); // Navegador desde el que se accede (ej. Chrome, Firefox)
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
