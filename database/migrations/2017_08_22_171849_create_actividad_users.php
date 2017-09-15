<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActividadUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('MODULOS_GRUPO_SIZ', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('id_grupo');
                $table->integer('id_modulo')->nullable();
                $table->integer('id_tarea')->nullable();
                $table->timestamps();
            });
        Schema::create('MENU_ITEM_SIZ', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('id_modulo');
            $table->timestamps();
        });
        Schema::create('TAREA_MENU_SIZ', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('id_menu_item');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('MODULOS_GRUPO_SIZ');
        Schema::drop('MENU_ITEM_SIZ');
        Schema::drop('TAREA_MENU_SIZ');
    }
}
