<?php

namespace CryptaTech\Seat\Fitting\Events;

use CryptaTech\Seat\Fitting\Models\Fitting;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FittingUpdated
{
    use Dispatchable, SerializesModels;

    public Fitting $fitting;

    public function __construct(Fitting $fitting)
    {
        $this->fitting = $fitting;
    }
}
