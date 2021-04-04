<?php
namespace TurboLabIt\TLIBaseBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;


class TLIBaseBundleExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container,  new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
