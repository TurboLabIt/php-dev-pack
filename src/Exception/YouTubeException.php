<?php
namespace TurboLabIt\TLIBaseBundle\Exception;


class YouTubeException extends BaseException
{
    protected $code = Response::HTTP_INTERNAL_SERVER_ERROR;
    protected $severity = self::SEVERITY_CRITICAL;
}
