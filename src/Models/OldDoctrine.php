<?php

namespace CryptaTech\Seat\Fitting\Models;

use Illuminate\Database\Eloquent\Model;

class OldDoctrine extends Model
{
    public $timestamps = true;

    protected $table = 'seat_doctrine';

    protected $fillable = ['id', 'name', 'role_id'];

    public function fittings()
    {
        return $this->belongsToMany(OldFitting::class, 'seat_doctrine_fitting', 'doctrine_id', 'fitting_id');
    }
}
