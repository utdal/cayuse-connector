<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class NotFoundHttpException extends Exception
{
    public function __construct(Throwable $previous = null) {
        parent::__construct('Not Found', 404, $previous);
    }
}