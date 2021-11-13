<?php
namespace TurboLabIt\TLIBaseBundle\Service\FileType;

use Symfony\Component\Config\Util\Exception\InvalidXmlException;


class Xml
{
    public function prettify(string $txtXml, bool $removeXMLHeader = false)
    {
        $oXml = new \DomDocument('1.0');
        $oXml->preserveWhiteSpace = false;
        $oXml->formatOutput = true;
        $loadResult = @$oXml->loadXML($txtXml);

        if($loadResult === false ) {
            throw new InvalidXmlException("Xml::prettify() was unable to parse the provided string as XML");
        }

        $prettyXml = $oXml->saveXML();

        if($removeXMLHeader) {
            $prettyXml = preg_replace('/^.+' . PHP_EOL . '/', '', $prettyXml);
        }

        return $prettyXml;
    }
}
