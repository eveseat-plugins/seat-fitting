<?php

/*
 * This file derived from SeAT
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

namespace CryptaTech\Seat\Fitting\Models;

use CryptaTech\Seat\Fitting\Models\Sde\InvFlag;
use Illuminate\Database\Eloquent\Model;
use Seat\Eveapi\Models\Sde\InvType;
use Seat\Services\Contracts\HasTypeIDWithAmount;
use Seat\Services\Contracts\IPriceable;

/**
 * Class FittingItem.
 *
 * @property InvType $type
 * @property InvFlag $invFlag
 */
class FittingItem extends Model implements HasTypeIDWithAmount, IPriceable
{
    /**
     * @var bool
     */
    protected static $unguarded = true;

    /**
     * @var string
     */
    protected $primaryKey = 'fitting_item_id';

    protected $fillable = ['type_id', 'flag', 'fitting_id', 'quantity'];

    protected $table = 'crypta_tech_seat_fitting_items';

    protected float $price;

    public function type()
    {
        return $this->hasOne(InvType::class, 'typeID', 'type_id');
    }

    public function invFlag()
    {
        return $this->hasOne(InvFlag::class, 'flagID', 'flag');
    }

    /**
     * @return int The eve type id of this object
     */
    public function getTypeID(): int
    {
        return $this->type_id;
    }

    /**
     * @return int The amount of this item to be appraised
     */
    public function getAmount(): int
    {
        return $this->quantity;
    }

    /**
     * @return float The price of this item stack
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param  float  $price  The new price of this item stack
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
}
