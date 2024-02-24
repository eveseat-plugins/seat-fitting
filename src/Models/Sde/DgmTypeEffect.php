<?php


namespace CryptaTech\Seat\Fitting\Models\Sde;

use Illuminate\Database\Eloquent\Model;
use Seat\Eveapi\Traits\HasCompositePrimaryKey;
use Seat\Eveapi\Traits\IsReadOnly;

/**
 * Class DgmEffect
 *
 * @package CryptaTech\Seat\Fitting\Models\Sde
 */
class DgmTypeEffect extends Model
{
    use IsReadOnly;
    use HasCompositePrimaryKey;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $table = 'dgmTypeEffects';

    /**
     * @var string
     */
    protected $primaryKey = ['typeID', 'effectID'];

    /**
     * @var bool
     */
    public $timestamps = false;
}
