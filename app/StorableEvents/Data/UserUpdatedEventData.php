<?php

namespace App\StorableEvents\Data;

class UserUpdatedEventData
{
    public function __construct(public $name, public $surname, public $nickname)
    {
    }
}
