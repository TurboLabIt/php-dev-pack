<?php
namespace TurboLabIt\TLIBaseBundle\Exception;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;


abstract class BaseException extends \RuntimeException
{
    const SEVERITY_CRITICAL = 'critical';
    const SEVERITY_HIGH     = 'alert';
    const SEVERITY_MEDIUM   = 'warning';
    const SEVERITY_LOW      = 'notice';

    protected $category     = 'undefined';
    protected $severity     = 'alert';
    protected $code         = Response::HTTP_BAD_REQUEST;


    public function __construct(
        protected ?LoggerInterface $logger = null,
        protected array $arrData = [],
        protected string $extraMessage = '',
        \Throwable $previous = null
    )
    {
        parent::__construct($extraMessage, $this->code, $previous);
    }


    public function setSeverity(string $severity) : static
    {
        $this->severity = $severity;
        $this->buildMessage();
        return $this;
    }


    public function setData(array $arrData = []) : static
    {
        $this->arrData = $arrData;
        $this->buildMessage();
        return $this;
    }


    public function log() : static
    {
        $this->buildMessage();

        if( $this->logger instanceof LoggerInterface ) {
            $this->logger->{$this->severity}($this->getMessage());
        }

        return $this;
    }


    public function getSeverityText() : string
    {
        $text = match($this->severity) {
            static::SEVERITY_CRITICAL   => '🚨',
            static::SEVERITY_HIGH      => '🔥',
            static::SEVERITY_MEDIUM    => '📯',
            static::SEVERITY_LOW       => '⚠',
            default                     => '📌'
        };

        $text .= ' ' . $this->severity;
        return $text;
    }


    public function buildMessage() : string
    {
        $message = '';
        foreach($this->getMessageComponents() as $key => $value) {

            if( !empty($value) ) {
                $message .= $key . ': ##' . trim(strip_tags($value)) . "## | ";
            }
        }

        $this->message = trim($message);
        return $this->message;
    }


    public function getMessageComponents() : array
    {
        return array_merge_recursive([
            "Category"  => $this->category,
            "Severity"  => $this->getSeverityText(),
            "Message"   => $this->extraMessage
        ], $this->arrData);
    }
}
