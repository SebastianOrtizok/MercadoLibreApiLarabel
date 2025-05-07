<?php

  use Illuminate\Database\Migrations\Migration;
  use Illuminate\Database\Schema\Blueprint;
  use Illuminate\Support\Facades\Schema;

  class AddOfficialStoreIdFieldToCompetidores extends Migration
  {
      public function up()
      {
          Schema::table('competidores', function (Blueprint $table) {
              $table->integer('official_store_id')->nullable()->after('nombre');
          });
      }

      public function down()
      {
          Schema::table('competidores', function (Blueprint $table) {
              $table->dropColumn('official_store_id');
          });
      }
  }
