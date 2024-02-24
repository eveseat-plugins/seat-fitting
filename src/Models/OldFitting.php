<?php

namespace CryptaTech\Seat\Fitting\Models;

use Illuminate\Database\Eloquent\Model;

class OldFitting extends Model
{
    public $timestamps = true;

    protected $table = 'seat_fitting';

    protected $fillable = ['id', 'shiptype', 'fitname', 'eftfitting'];

    public function doctrines()
    {
        return $this->belongsToMany(Doctrine::class, 'seat_doctrine_fitting');

    }
}
