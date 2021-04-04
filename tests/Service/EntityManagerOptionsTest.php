<?php declare(strict_types=1);
namespace TurboLabIt\TLIBaseBundle\tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use TurboLabIt\TLIBaseBundle\Service\EntityManagerOptions;
use TurboLabIt\TLIBaseBundle\tests\TLIBaseTestingKernel;


final class EntityManagerOptionsTest extends TestCase
{
    protected EntityManagerInterface $em;
    protected EntityManagerOptions $emOptions;


    public function testAutowire()
    {
        $kernel = new TLIBaseTestingKernel('test', true);
        $kernel->boot();
        $container = $kernel->getContainer();

        $this->emOptions = $container->get('turbo_lab_it_tlibase.service.entity_manager_options');
        $this->assertInstanceOf(EntityManagerOptions::class, $this->emOptions);
    }
}
