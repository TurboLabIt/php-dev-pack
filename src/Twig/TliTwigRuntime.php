<?php
namespace TurboLabIt\TLIBaseBundle\Twig;

use Twig\Extension\RuntimeExtensionInterface;


class TliTwigRuntime implements RuntimeExtensionInterface
{
    public function trim(string $input): string
    {
        return trim($input);
    }
    
    
    public function friendlyNum(string|float|int $input): string
    {
        $locale = empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? null : \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $locale = $locale ?? 'it_IT';
        return (new \NumberFormatter($locale, \NumberFormatter::DECIMAL))->format($input);
    }
    
    
    public function friendlyDate(\DateTime $date = null): ?string
    {
        if( empty($date) ) {
            return null;
        }

        $oNow = new \DateTime();
        $secDiff = $oNow->getTimestamp() - $date->getTimestamp();
        $oneDayInSec = 3600 * 24;

        $stopFriendlyness = $oneDayInSec * 2;
        if( $secDiff < 0 || $secDiff > $stopFriendlyness ) {
            return $date->format('d/m/Y') . ' alle ' . $date->format('H:i');
        }

        if( $secDiff >= $oneDayInSec ) {
            return 'ieri alle ' . $date->format('H:i');
        }

        $oneHourInSec = 3600;
        if( $secDiff >= $oneHourInSec ) {
            $num    = (int)floor($secDiff / 3600);
            $word   = $num == 1 ? 'ora' : 'ore';
            return $num . ' ' . $word . ' fa';
        }

        $stopNow = 60 * 30;
        if( $secDiff >= $stopNow ) {
            $num    = (int)floor($secDiff / 60);
            return $num . ' minuti fa';
        }

        return 'adesso';
    }
}
