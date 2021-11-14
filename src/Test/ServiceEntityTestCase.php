<?php
namespace TurboLabIt\TLIBaseBundle\Test;


abstract class ServiceEntityTestCase extends TLIWebTestBase
{
    protected $testedEntityName = null;

    
    public function testGetInstance()
    {
        $instance   = $this->getService($this->testedServiceName);
        $expected   = $this->testedServiceName;
        $this->assertInstanceOf($expected, $instance);
        return $instance;
    }


    public function testGetRandomRecord($testedEntityName = null)
    {
        $testedEntityName = $testedEntityName ?: $this->testedEntityName;

        $allEntities = $this->getRepository($testedEntityName)->findAll();

        shuffle($allEntities);

        $entity = reset($allEntities);

        $expected   = $this->testedEntityName;
        $this->assertInstanceOf($expected, $entity);
        return $entity;

    }


    public function testCountRecord($testedEntityName = null, int $minNum = 10)
    {
        $testedEntityName = $testedEntityName ?: $this->testedEntityName;

        $num =
            $this->getRepository($testedEntityName)
                ->createQueryBuilder('t')
                ->select('COUNT(1)')
                ->getQuery()
                ->getSingleScalarResult();

        $this->assertGreaterThan($minNum, $num);
    }


    public function getLastRecord($testedEntityName = null)
    {
        $testedEntityName = $testedEntityName ?: $this->testedEntityName;

        $entity =
            $this->getRepository($testedEntityName)
                ->createQueryBuilder('t')
                ->orderBy('t.id', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        $expected   = $this->testedEntityName;
        $this->assertInstanceOf($expected, $entity);
        return $entity;
    }


    public function testLoadBySlugId(?string $slugId = null)
    {
        if( empty($slugId) ) {

            $randomEntity   = $this->testGetRandomRecord();
            $slugId         = "test-" . $randomEntity->getId();
        }

        $instance = $this->getInstance()->loadBySlugId($slugId);
        $expected   = $this->testedServiceName;
        $this->assertInstanceOf($expected, $instance);
        return $instance;
    }
}
