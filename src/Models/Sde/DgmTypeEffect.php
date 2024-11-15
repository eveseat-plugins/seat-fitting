<?php

namespace CryptaTech\Seat\Fitting\Models\Sde;

use Illuminate\Database\Eloquent\Model;
use Seat\Eveapi\Traits\HasCompositePrimaryKey;
use Seat\Eveapi\Traits\IsReadOnly;

/**
 * Class DgmEffect.
 */
class DgmTypeEffect extends Model
{
    use HasCompositePrimaryKey;
    use IsReadOnly;

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
