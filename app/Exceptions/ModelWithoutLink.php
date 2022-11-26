<?php

namespace App\Exceptions;

use Exception;

class ModelWithoutLink extends Exception
{
    public function __construct()
    {
        parent::__construct("Couldn't get model's link property", 500);
    }
}
