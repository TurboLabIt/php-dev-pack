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
        $locale = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']) ?? 'it_IT';
        return (new \NumberFormatter($locale, \NumberFormatter::DECIMAL))->format($input);
    }
    
    
    public function friendlyDate(\DateTime $date): string
    {
        return $date->format('Y-m-d');
    }
}
