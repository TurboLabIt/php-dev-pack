<?php
namespace TurboLabIt\TLIBaseBundle\Test;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class BaseTest extends KernelTestCase
{
    protected static $className;
  
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;


    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testInstance(): void
    {
        $instance = $this->getService();
        $this->assertInstanceOf(self::$className, $instance);
    }


    protected function getService()
    {
        return self::getContainer()->get(self::$className);
    }


    /*
    protected function getRandomStation()
    {
        return $this->getRandomRecord(Station::class);
    }
    */

    protected function getRandomRecord($entityName)
    {
        return
            $this->entityManager->getRepository($entityName)
                ->createQueryBuilder('t')
                    ->orderBy('RAND()')
                    ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
    }
}
