<?php
namespace TurboLabIt\TLIBaseBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TrimmerExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('trimmer', [$this, 'trim']),
        ];
    }


    public function trim(string $value): string
    {
        return trim($value);
    }
}
