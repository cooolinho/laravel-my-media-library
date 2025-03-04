<?php

namespace App\Jobs;

use Exception;

class JobNotActivatedException extends Exception
{
    protected $message = 'job is disabled via settings';
}
