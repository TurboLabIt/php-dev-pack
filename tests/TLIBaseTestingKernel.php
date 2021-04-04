<?php
namespace TurboLabIt\TLIBaseBundle\tests;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use TurboLabIt\TLIBaseBundle\TLIBaseBundle;


class TLIBaseTestingKernel extends Kernel
{
    public function registerBundles()
    {
        return [
            new TLIBaseBundle()
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }
}
