<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;


class CreateCryptaSeatDoctrineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $fits = DB::table('crypta_tech_seat_fittings')->get(['fitting_id', 'name']);
        foreach ($fits as $fit) {
            DB::table('crypta_tech_seat_fittings')
                ->where('fitting_id', $fit->fitting_id)
                ->update(['name' => trim($fit->name)]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
