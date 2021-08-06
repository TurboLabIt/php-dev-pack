<?php
namespace TurboLabIt\TLIBaseBundle\Service\ServiceEntity;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use TurboLabIt\TLIBaseBundle\Exception\EntityLoadNotFoundException;
use TurboLabIt\TLIBaseBundle\Exception\WrongTypeException;


abstract class ServiceEntity
{
    protected EntityManager $em;
    protected ServiceEntityRepository $repository;

    protected $entity;
    protected array $arrData = [];
    protected bool $isSelected = false;


    public function __construct(EntityManagerInterface $em, string $entityClassName)
    {
        $this->em           = $em;
        $this->repository   = $this->em->getRepository($entityClassName);
        $this->entity       = new $entityClassName();
    }


    public function loadBySlugId(string $slugId)
    {
        $arrSlugId = $this->unpackSlugId($slugId);
        if( empty($arrSlugId) ){
            return $this->throwNotFoundException();
        }

        return $this->loadById($arrSlugId["id"]);
    }


    public function loadById(int $id)
    {
        return $this->loadByFieldsValues(['id' => $id]);
    }


    public function loadByFieldsValues(array $arrFieldsValues)
    {
        $this->reset();

        $entity = $this->repository->findOneBy($arrFieldsValues);
        if (empty($entity) ) {

            $this->throwNotFoundException();
        }

        return $this->setEntity($entity);
    }


    public function fakeLoadBySlugId(string $slugId)
    {
        $arrSlugId = $this->unpackSlugId($slugId);
        if( empty($arrSlugId) ){
            return $this->throwNotFoundException();
        }

        return $this->fakeLoadById($arrSlugId["id"]);
    }


    public function fakeLoadById(int $id)
    {
        $this->reset();
        $this->entity->setId($id);
        return $this;
    }


    public function setEntity($entity)
    {
        if( empty($entity) ) {

            return $this->throwNotFoundException();
        }

        $doctrineProxyPrefix    = 'Proxies\\__CG__\\';
        $baseEntityType         = str_ireplace($doctrineProxyPrefix, '', get_class($this->entity));
        $arrAcceptedEntity      = [ $doctrineProxyPrefix . $baseEntityType, $baseEntityType ];

        $receivedEntity         = get_class($entity);
        if( !in_array($receivedEntity, $arrAcceptedEntity) ) {

            throw new WrongTypeException("Expected: " . implode(' | ', $arrAcceptedEntity) . " | Received: " . $receivedEntity);
        }

        $this->entity = $entity;
        return $this;
    }


    public function getEntity()
    {
        return $this->entity;
    }


    public function setData(array $arrData)
    {
        $this->arrData = $arrData;
        return $this;
    }


    public function getData($index = null)
    {
        if( $index === null ) {

            return $this->arrData;
        }

        if( array_key_exists($index, $this->arrData) ) {

            return $this->arrData[$index];
        }

        return null;
    }


    protected function throwNotFoundException()
    {
        throw new EntityLoadNotFoundException();
    }


    public function setSelected(bool $isIt = true)
    {
        $this->isSelected = $isIt;
        return $this;
    }


    public function isSelected(): bool
    {
        return $this->isSelected;
    }


    public function getAsArray(array $options = []): array
    {
        return array_merge($this->getData(), [

            "id" => $this->entity->getId()
        ]);
    }


    public function unpackSlugId(string $txtSlugId): ?array
    {
        $lastDash = strrpos($txtSlugId, '-');
        if( $lastDash === false ){

            return null;
        }

        $slug   = substr($txtSlugId, 0, $lastDash);
        $id     = substr($txtSlugId, $lastDash + 1);

        if( preg_match('/^[1-9][0-9]*$/', $id) === false ) {

            return null;
        }

        return [
            "slug"  => $slug,
            "id"    => $id
        ];
    }


    public function increase($field)
    {
        $this->repository->atomicFieldIncrease($field, $this->getId());
    }


    public function save(bool $autoflush = false)
    {
        $this->em->persist($this->entity);

        if($autoflush) {

            $this->em->flush();
        }

        return $this;
    }


    public function reset(): static
    {
        $entityClassName    = get_class($this->entity);
        $this->entity       = new $entityClassName();
        $this->arrData      = [];
        $this->isSelected   = false;

        return $this;
    }


    public function __call(string $name, array $arguments)
    {
        // looking in "data" first
        $fromData = $this->getData($name);
        if( $fromData !== null ) {

            return $fromData;
        }

        // handling a set on the entity
        if( stripos($name, 'set') === 0 ) {

            $this->entity->$name(...$arguments);
            return $this;
        }

        // handling a function call without "get"
        if( !method_exists($this->entity, $name) && stripos($name, 'get') !== 0 ) {

            $name = 'get' . ucfirst($name);
        }

        // if the method still doesn't exists on the entity => throw a specific exception to notify the developer
        if( !method_exists($this->entity, $name) ) {

            throw new UndefinedMagicMethodException($name);
        }

        return $this->entity->$name(...$arguments);
    }
}
