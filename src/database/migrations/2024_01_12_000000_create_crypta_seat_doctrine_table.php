<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCryptaSeatDoctrineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crypta_tech_seat_fitting_doctrine', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('crypta_tech_seat_doctrine_fitting', function (Blueprint $table) {
            $table->unsignedInteger('doctrine_id');
            $table->bigInteger('fitting_id')->unsigned();

            $table->foreign('fitting_id')
                ->references('fitting_id')
                ->on('crypta_tech_seat_fittings')
                ->onDelete('cascade');

            $table->foreign('doctrine_id')
                ->references('id')
                ->on('crypta_tech_seat_fitting_doctrine')
                ->onDelete('cascade');
        });

        Schema::create('crypta_tech_seat_doctrine_role', function (Blueprint $table) {
            $table->unsignedInteger('doctrine_id');
            $table->unsignedInteger('role_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crypta_tech_seat_doctrine_role');
        Schema::dropIfExists('crypta_tech_seat_doctrine_fitting');
        Schema::dropIfExists('crypta_tech_seat_fitting_doctrine');
    }
}
