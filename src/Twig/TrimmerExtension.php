<?php
namespace TurboLabIt\TLIBaseBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;


class TrimmerExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('trimmer', [TrimmerRuntime::class, 'trim'], ['is_safe' => ['html']])
        ];
    }
}
