<?php


namespace CryptaTech\Seat\Fitting\Models\Sde;

use Illuminate\Database\Eloquent\Model;
use Seat\Eveapi\Traits\IsReadOnly;

/**
 * Class InvFlag.
 *
 * @package CryptaTech\Seat\Fitting\Models\Sde
 */
class InvFlag extends Model
{
    use IsReadOnly;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $table = 'invFlags';

    /**
     * @var string
     */
    protected $primaryKey = 'flagID';

    /**
     * @var bool
     */
    public $timestamps = false;

}