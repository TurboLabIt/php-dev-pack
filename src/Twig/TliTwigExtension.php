<?php
namespace TurboLabIt\TLIBaseBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;


class TliTwigExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('trimmer', [TliTwigRuntime::class, 'trim'], ['is_safe' => ['html']]),
            new TwigFilter('friendlyNum', [TliTwigRuntime::class, 'friendlyNum'], ['is_safe' => ['html']]),
            new TwigFilter('friendlyDate', [TliTwigRuntime::class, 'friendlyDate'], ['is_safe' => ['html']]),
        ];
    }
}
