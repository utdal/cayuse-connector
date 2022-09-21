<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class NotFoundViewException extends Exception
{
    public function __construct(Throwable $previous = null) {
        parent::__construct('View Not Found', 500, $previous);
    }
}