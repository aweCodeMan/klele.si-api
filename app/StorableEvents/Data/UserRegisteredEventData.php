<?php

namespace App\StorableEvents\Data;

class UserRegisteredEventData
{
    public function __construct(public $name, public $surname, public $nickname, public $email)
    {
    }
}
