<?php

namespace CryptaTech\Seat\Fitting\Models;

use Illuminate\Database\Eloquent\Model;
use Seat\Web\Models\Acl\Role;

class Doctrine extends Model
{
    public $timestamps = true;

    protected $table = 'crypta_tech_seat_fitting_doctrine';

    protected $fillable = ['id', 'name', 'role_id'];

    public function fittings()
    {
        return $this->belongsToMany(Fitting::class, 'crypta_tech_seat_doctrine_fitting', 'doctrine_id', 'fitting_id');
    }

    // Not yet used
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'crypta_tech_seat_doctrine_role');
    }
}
