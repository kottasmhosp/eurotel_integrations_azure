<?php


namespace App\Utils\Exception;


use Exception;
use Throwable;

class EmptyContentException extends Exception
{
    public function __construct($message = "empty content exception", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}