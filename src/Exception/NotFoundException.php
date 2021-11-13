<?php
namespace TurboLabIt\TLIBaseBundle\Exception;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


abstract class NotFoundException extends NotFoundHttpException
{
    const SEVERITY_CRITICAL = 'critical';
    const SEVERITY_HIGH     = 'alert';
    const SEVERITY_MEDIUM   = 'warning';
    const SEVERITY_LOW      = 'notice';

    protected Request|null $request;

    protected $category     = 'undefined';
    protected $severity     = 'alert';


    public function __construct(
        protected LoggerInterface $logger,
        protected array $arrData = [],
        ?RequestStack $requestStack = null,
        protected ?string $requestUrl = null,
        protected ?string $referrer = null,
        \Throwable $previous = null, int $code = 0, array $headers = [],
    ) {
        $this->setPropertyFromRequest($requestStack, $requestUrl, $referrer);

        $message = $this->buildMessage();
        parent::__construct($message, $previous, $code, $headers);
    }


    public function setPropertyFromRequest(?RequestStack $requestStack, ?string $requestUrl, ?string $referrer) : static
    {
        $request = $requestStack instanceof RequestStack ? $requestStack->getCurrentRequest() : null;

        if( !empty($requestUrl) ) {

            $this->requestUrl = trim(strip_tags($requestUrl));

        } elseif ( !empty($request) ) {

            $this->requestUrl = trim(strip_tags($request->getUri()));
        }

        if( !empty($referrer) ) {

            $this->referrer = trim(strip_tags($referrer));

        } elseif ( !empty($request) ) {

            $this->referrer = trim(strip_tags($request->headers->get('referer')));
        }
        
        return $this;
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
        $this->logger->{$this->severity}($this->getMessage());
        return $this;
    }


    public function buildMessage() : string
    {
        $arrInfo = array_merge_recursive([
            "Category"  => $this->category,
            "Severity"  => $this->getSeverityText(),
            "URL"       => $this->requestUrl,
            "Referrer"  => $this->referrer
        ], $this->arrData);
        
        $message = '';
        foreach($arrInfo as $key => $value) {
            
            if( !empty($value) ) {
                $message .= $key . ': ##' . trim(strip_tags($value)) . "## | ";
            }
        }

        $this->message = trim($message);
        return $this->message;
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
}
