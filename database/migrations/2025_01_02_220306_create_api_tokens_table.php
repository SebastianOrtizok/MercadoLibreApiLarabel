<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id(); // Clave primaria
            $table->unsignedBigInteger('user_id'); // Relación con usuarios, si aplica
            $table->string('access_token'); // Token de acceso
            $table->string('refresh_token')->nullable(); // Token de refresco
            $table->timestamp('expires_at')->nullable(); // Fecha de expiración
            $table->timestamps(); // created_at y updated_at


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_tokens');
    }
}
