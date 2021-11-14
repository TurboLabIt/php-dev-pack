<?php
namespace TurboLabIt\TLIBaseBundle\Exception;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


abstract class NotFoundException extends BaseException
{
    protected $code = Response::HTTP_NOT_FOUND;
    protected Request|null $request;

    public function __construct(
        ?LoggerInterface $logger,
        array $arrData = [],
        string $extraMessage = '',
        ?RequestStack $requestStack = null,
        protected ?string $requestUrl = null,
        protected ?string $referrer = null,
        \Throwable $previous = null
    ) {
        $this->setPropertyFromRequest($requestStack, $requestUrl, $referrer);
        parent::__construct($logger, $arrData, $extraMessage, $previous);
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


    public function getMessageComponents() : array
    {
        return array_merge_recursive(parent::getMessageComponents(), [
            "URL"       => $this->requestUrl,
            "Referrer"  => $this->referrer
        ]);
    }
}
