<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompetidoresTable extends Migration
{
    public function up()
    {
        Schema::create('competidores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('seller_id');
            $table->string('nickname')->nullable();
            $table->string('nombre')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('seller_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('competidores');
    }
}
