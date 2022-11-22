<?php

namespace App\Exceptions;

use Exception;

class LecturerNotFound extends Exception
{
    public function __construct()
    {
        parent::__construct("Lecturer not found", 500);
    }
}
