<?php

namespace App\Exceptions;

use Exception;

class CommandFailedException extends Exception
{
    public function __construct()
    {
        parent::__construct("Command failed", 500);
    }
}
