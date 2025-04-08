<?php

/*
 * This file is expanded from SeAT
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

use CryptaTech\Seat\Fitting\Models\Sde\DgmTypeEffect;
use Illuminate\Database\Eloquent\Model;
use Seat\Eveapi\Models\Sde\DgmTypeAttribute;
use Seat\Eveapi\Models\Sde\InvType;

/**
 * Class Fitting.
 */
class Fitting extends Model
{
    // Index of where the slots start
    const SLOT_LOW = 11;

    const SLOT_MEDIUM = 19;

    const SLOT_HIGH = 27;

    const SLOT_RIG = 92;

    const SLOT_SUBSYSTEM = 125;

    const INDEX_SLOT_MAX = 7;

    const BAY_DRONE = 87;

    const BAY_FIGHTER = 158;

    const BAY_CARGO = 5;

    const IMPLANT = 89;

    const SKILL = 7;

    public $timestamps = true;

    protected $table = 'crypta_tech_seat_fittings';

    protected $fillable = ['name', 'description', 'ship_type_id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'fitting_id';

    /**
     * @var bool
     */
    protected static $unguarded = true;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {

        return $this->hasMany(
            FittingItem::class,
            'fitting_id',
            'fitting_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function ship()
    {

        return $this->hasOne(InvType::class, 'typeID', 'ship_type_id')
            ->withDefault([
                'typeName' => trans('web::seat.unknown'),
            ]);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getFitItemsAttribute()
    {
        return $this->items->whereNotIn('flag', [Fitting::IMPLANT, Fitting::SKILL]);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getHighSlotsAttribute()
    {
        return $this->fitItems->filter(function ($value) {
            return ($value->flag >= Fitting::SLOT_HIGH) && ($value->flag <= Fitting::SLOT_HIGH + Fitting::INDEX_SLOT_MAX);
        });
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getMediumSlotsAttribute()
    {
        return $this->fitItems->filter(function ($value) {
            return ($value->flag >= Fitting::SLOT_MEDIUM) && ($value->flag <= Fitting::SLOT_MEDIUM + Fitting::INDEX_SLOT_MAX);
        });
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getLowSlotsAttribute()
    {
        return $this->fitItems->filter(function ($value) {
            return ($value->flag >= Fitting::SLOT_LOW) && ($value->flag <= Fitting::SLOT_LOW + Fitting::INDEX_SLOT_MAX);
        });
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getRigSlotsAttribute()
    {
        return $this->fitItems->filter(function ($value) {
            return ($value->flag >= Fitting::SLOT_RIG) && ($value->flag <= Fitting::SLOT_RIG + Fitting::INDEX_SLOT_MAX);
        });
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getSubSystemsAttribute()
    {
        return $this->fitItems->filter(function ($value) {
            return ($value->flag >= Fitting::SLOT_SUBSYSTEM) && ($value->flag <= Fitting::SLOT_SUBSYSTEM + Fitting::INDEX_SLOT_MAX);
        });
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getDronesBayAttribute()
    {
        return $this->fitItems->where('flag', Fitting::BAY_DRONE);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getFightersBayAttribute()
    {
        return $this->fitItems->where('flag', Fitting::BAY_FIGHTER);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getCargoAttribute()
    {
        return $this->fitItems->where('flag', Fitting::BAY_CARGO);
    }

    /**
     * @return float
     */
    public function getEstimatedPriceAttribute()
    {
        return $this->ship->price->adjusted_price + $this->fitItems->sum(function ($item) {
            return $item->type->price->adjusted_price * $item->quantity;
        });
    }

    /**
     * @return float
     */
    public function getFittingEstimatedPriceAttribute()
    {
        return $this->fitItems->sum(function ($item) {
            return $item->type->price->adjusted_price * $item->quantity;
        });
    }

    /**
     * @return string
     */
    public function toEve()
    {
        return sprintf('[%s, %s]', $this->ship->typeName, $this->name).PHP_EOL.

            $this->low_slots->map(function ($slot) {
                return $slot->type->typeName;
            })->implode(PHP_EOL).

            PHP_EOL.PHP_EOL.

            $this->medium_slots->map(function ($slot) {
                return $slot->type->typeName;
            })->implode(PHP_EOL).

            PHP_EOL.PHP_EOL.

            $this->high_slots->map(function ($slot) {
                return $slot->type->typeName;
            })->implode(PHP_EOL).

            PHP_EOL.PHP_EOL.

            $this->sub_systems->map(function ($slot) {
                return $slot->type->typeName;
            })->implode(PHP_EOL).

            PHP_EOL.PHP_EOL.

            $this->rig_slots->map(function ($slot) {
                return $slot->type->typeName;
            })->implode(PHP_EOL).

            PHP_EOL.PHP_EOL.

            $this->drones_bay->map(function ($slot) {
                return sprintf('%s x%d', $slot->type->typeName, $slot->quantity);
            })->implode(PHP_EOL).

            PHP_EOL.PHP_EOL.

            $this->cargo->map(function ($slot) {
                return sprintf('%s x%d', $slot->type->typeName, $slot->quantity);
            })->implode(PHP_EOL).

            PHP_EOL.PHP_EOL.

            $this->fighters_bay->map(function ($slot) {
                return sprintf('%s x%d', $slot->type->typeName, $slot->quantity);
            })->implode(PHP_EOL).

            PHP_EOL.PHP_EOL;
    }

    public static function createFromEve(string $eft, ?int $existing_id = null): Fitting
    {

        // Normalise all the line endings to \n
        $eft = preg_replace('~\r\n?~', "\n", $eft);

        $data = explode("\n", $eft);

        // Now look at the header line as we know it must be first
        $header = array_shift($data);
        [$shipname, $name] = explode(',', substr($header, 1, -1));

        $ship = InvType::where('typeName', $shipname)->first();

        $fit = new Fitting; // Get top level scope

        if ($existing_id > 0) {
            $fit = Fitting::find($existing_id);
            $fit->items()->delete();
        }

        $fit->name = trim($name);
        $fit->description = '';
        $fit->ship_type_id = $ship->typeID;

        $fit->save();

        // This is our current parser state
        $state = STATE::LOWS;
        // $state = STATE::from($state->value +1); <-- Used to increment states

        $index = 0;
        foreach ($data as $line) {
            if (empty($line)) {
                continue;
            }

            // Split away to makek sure we only have the main item first.
            $mod = explode(',', $line);
            $modu = explode(' x', $mod[0]);
            $module = InvType::where('typeName', $modu[0])->first();
            if (empty($module)) {
                continue;
            }

            // Here is where we update our state machine to the next state if required.
            $solved = false;
            while (! $solved && ($state != $state->nextState())) {
                if ($state->validInvType($module)) {
                    $solved = true;
                } else {
                    $index = 0;
                    $state = $state->nextState();
                }
            }

            // The state should now be correct :)
            FittingItem::create([
                'fitting_id' => $fit->fitting_id,
                'type_id' => $module->typeID,
                'flag' => $state->getFlag($index),
                'quantity' => isset($modu[1]) ? $modu[1] : 1,
            ]);
            $index += 1;

            // Now check if we have anything in the charges and add to cargo if so.
            if (isset($mod[1])) {
                // we have something split for qty
                $chg = explode(' x', trim($mod[1]));
                $charge = InvType::where('typeName', $chg[0])->first();
                if (! empty($charge)) {
                    FittingItem::create([
                        'fitting_id' => $fit->fitting_id,
                        'type_id' => $charge->typeID,
                        'flag' => Fitting::BAY_CARGO,
                        'quantity' => isset($chg[1]) ? $chg[1] : 1,
                    ]);
                }
            }
        }

        // Now, if this is a ship that has a fighter bay, do a pass back over and move fighters from cargo to the fighter bay.
        // There is probably more efficient ways to do this all in the DB in one update, but cbf as it wont run often.
        // Though may be more of an issue if people depend on observers....... But this whole serial approach falls apart then anyway.
        $fighterHangarCapacity = DgmTypeAttribute::where('typeID', $fit->ship_type_id)->where('attributeID', 2055)->value('valueFloat');
        if ($fighterHangarCapacity) {
            // Fighter Capable!
            $hold = FittingItem::where('fitting_id', $fit->fitting_id)->where('flag', Fitting::BAY_CARGO)->get();
            foreach ($hold as $cargo) {
                if ($cargo->type()->first()->group()->first()->categoryID == 87) { // This is a fighter
                    $cargo->flag = Fitting::BAY_FIGHTER;
                    $cargo->save();
                }
            }
        }

        // laravel caches relations, meaning that with the way we add items to the fitting, the cache isn't invalidated automatically
        $fit->unsetRelations();

        return $fit;
    }
}

// Defined out here to be more usable and convenient.. Its public because it has to be. Not for external consumption!
/**
 * So what is this format? It's made up of a few sections that are separated with empty linebreaks:
 *    First line lists the ship and fitting name, separated by a comma (i.e., [Raven, karkur's little raven fit])
 *    Low slot modules
 *    Mid slot modules and charge (if available)
 *    High slot modules and charge (if available) (i.e., 125mm Railgun I, Antimatter Charge S)
 *    Rigs
 *    Subsystems
 *    Drones in drone bay with amount (i.e., Warrior II x2)
 *    Items in cargo bay with amount(i.e., Antimatter Charge M x1)
 *    Now the hard one.... Fighters... I think that because cargo is the catch all and last.. I need to do a pass later
 *      that for all ships with fighter bays, these get moved from cargo to the fighter bay.
 *    NOT DOING VOLUME CHECKS!
 *
 *  So how am I goign to parse this mess... Well its not technically correct but a state machine
 *  We know the header is first so that is easy.
 *  From there I am going to ignore blank lines (HEATHEN!) and populate slots in order from top to bottom based on the order they appear
 *  So if the first item we come across is a gun, then skip the lows and mediums.
 *  Then if all the slots (not based on hull but max possible) are full then we assume its in cargo. (ie 4 Rigs means 3 fit and 1 in cargo)
 *  Charges in weapons are automatically loaded into cargo
 */
enum STATE: int
{
    case LOWS = 1;
    case MIDS = 2;
    case HIGHS = 3;
    case RIGS = 4;
    case SUBS = 5;
    case DRONES = 6;
    case CARGO = 7;

    public function getFlag(int $index): int
    {
        if ($index >= 0 && $index <= Fitting::INDEX_SLOT_MAX) {
            return match ($this) {
                self::LOWS => Fitting::SLOT_LOW + $index,
                self::MIDS => Fitting::SLOT_MEDIUM + $index,
                self::HIGHS => Fitting::SLOT_HIGH + $index,
                self::RIGS => Fitting::SLOT_RIG + $index,
                self::SUBS => Fitting::SLOT_SUBSYSTEM + $index,
                self::DRONES => Fitting::BAY_DRONE,
                self::CARGO => Fitting::BAY_CARGO,
            };
        } else {
            return Fitting::BAY_CARGO;
        }
    }

    public function nextState(): STATE
    {
        return match ($this) {
            self::LOWS => self::MIDS,
            self::MIDS => self::HIGHS,
            self::HIGHS => self::RIGS,
            self::RIGS => self::SUBS,
            self::SUBS => self::DRONES,
            self::DRONES => self::CARGO,
            self::CARGO => self::CARGO,
        };
    }

    public function validInvType(InvType $type): bool
    {

        return match ($this) {
            self::CARGO => true,
            self::DRONES => $this->validDrone($type),
            default => $this->validEffectID($type)
        };
    }

    private static function validDrone(InvType $type): bool
    {
        return $type->group()->first()->categoryID == 18;
    }

    private function validEffectID(InvType $type): bool
    {

        return DgmTypeEffect::where('typeID', $type->typeID)
            ->where('effectID', $this->effectID())
            ->exists();
    }

    // These are effectIDs from dgmEffects that dicate which slot an item can fit into.
    private function effectID(): int
    {

        return match ($this) {
            self::LOWS => 11,
            self::MIDS => 13,
            self::HIGHS => 12,
            self::RIGS => 2663,
            self::SUBS => 3772,
            default => -1,
        };
    }
}
