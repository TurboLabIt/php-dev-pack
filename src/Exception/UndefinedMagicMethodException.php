<?php
namespace TurboLabIt\TLIBaseBundle\Exception;


class UndefinedMagicMethodException extends \Exception
{
    public function __construct(string $calledMethod, $code = 0, Throwable $previous = null)
    {
        $message = "UndefinedMagicMethodException: ##" . $calledMethod ."##";
        parent::__construct($message, $code, $previous);
    }
}
