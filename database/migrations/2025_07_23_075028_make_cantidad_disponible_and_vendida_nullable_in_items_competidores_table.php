<?php

   use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Support\Facades\Schema;

   class MakeCantidadDisponibleAndVendidaNullableInItemsCompetidoresTable extends Migration
   {
       public function up()
       {
           Schema::table('items_competidores', function (Blueprint $table) {
               $table->integer('cantidad_disponible')->nullable()->change();
               $table->integer('cantidad_vendida')->nullable()->change();
           });
       }

       public function down()
       {
           Schema::table('items_competidores', function (Blueprint $table) {
               $table->integer('cantidad_disponible')->nullable(false)->change();
               $table->integer('cantidad_vendida')->nullable(false)->change();
           });
       }
   }
