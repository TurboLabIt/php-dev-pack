<?php
namespace TurboLabIt\TLIBaseBundle\Exception;


class UndefinedMagicMethodException extends \Exception
{
    public function __construct($object, string $calledMethod, $code = 0, Throwable $previous = null)
    {
        $message = "UndefinedMagicMethodException: 📦 " . get_class($object) . " ⚡ ##" . $calledMethod ."##()";
        parent::__construct($message, $code, $previous);
    }
}
