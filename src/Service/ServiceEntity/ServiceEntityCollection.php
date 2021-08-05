<?php
namespace TurboLabIt\TLIBaseBundle\Service\ServiceEntity;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use TurboLabIt\TLIBaseBundle\Traits\Foreachable;


abstract class ServiceEntityCollection implements \Iterator, \Countable, \ArrayAccess
{
    use Foreachable;

    protected string $entityClassName;
    protected EntityManager $em;
    protected ServiceEntityRepository $repository;


    public function __construct(EntityManagerInterface $em, string $entityClassName)
    {
        $this->em           = $em;
        $this->repository   = $this->em->getRepository($entityClassName);
    }


    public function loadByIds(array $arrIds)
    {
        $arrEntities = $this->repository->getByIds($arrIds);
        return $this->loadFromEntities($arrEntities);
    }


    public function loadAll()
    {
        $arrEntities = $this->repository->getAll();
        return $this->loadFromEntities($arrEntities);
    }


    public function loadFromEntities($entities)
    {
        $this->arrData = [];
        foreach($entities as $entity) {

            $id = (string)$entity->getId();
            $this->arrData[$id] =
                $this->createService()
                    ->setEntity($entity);
        }

        return $this;
    }


    public function toCsv($separator = ', ', $method = 'getTitle'): string
    {
        $arrData = [];
        foreach($this->arrData as $oneItem) {

            $arrData[] = $oneItem->$method();
        }

        return implode($separator, $arrData);
    }


    public function contains($object): bool
    {
        if( empty($object) || empty($this->arrData) ) {

            return false;
        }

        foreach($this->arrData as $item) {

            if(
                get_class($item) == get_class($object) &&
                $item->getId()   == $object->getId()
            ) {
                return true;
            }
        }

        return false;
    }


    abstract protected function createService();
}
