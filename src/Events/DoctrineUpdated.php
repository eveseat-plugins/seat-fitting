<?php

namespace CryptaTech\Seat\Fitting\Events;

use CryptaTech\Seat\Fitting\Models\Doctrine;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DoctrineUpdated
{
    use Dispatchable, SerializesModels;

    public Doctrine $doctrine;

    public function __construct(Doctrine $doctrine)
    {
        $this->doctrine = $doctrine;
    }
}
