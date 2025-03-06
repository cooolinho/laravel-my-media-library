<?php

namespace App\Jobs\Exceptions;

use Exception;

class JobNotActivatedException extends Exception
{
    protected $message = 'job is disabled via settings';
}
