<?php

/*
 * This file is part of SeAT
 *
 * Copyright (C) 2015 to present Leon Jacobs
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crypta_tech_seat_fitting_items', function (Blueprint $table) {

            $table->bigIncrements('fitting_item_id');
            $table->bigInteger('fitting_id')->unsigned();
            $table->integer('type_id');
            $table->integer('flag');
            $table->integer('quantity');

            $table->index(['fitting_id', 'type_id', 'flag']);
            $table->index('fitting_id');
            $table->index('type_id');

            $table->timestamps();

            $table->foreign('fitting_id')
                ->references('fitting_id')
                ->on('crypta_tech_seat_fittings')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crypta_tech_seat_fitting_items');
    }
};
