<?php
namespace TurboLabIt\TLIBaseBundle\Twig;

use Twig\Extension\RuntimeExtensionInterface;


class TrimmerRuntime implements RuntimeExtensionInterface
{
    public function trim(string $value): string
    {
        return trim($value);
    }
}
