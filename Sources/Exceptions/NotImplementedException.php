<?php

namespace UT_Php_Core\Exceptions;

class NotImplementedException extends \Exception
{
    /**
     * @param  string          $message
     * @param  int             $code
     * @param  \Throwable|null $previous
     * @return \Exception
     */
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
