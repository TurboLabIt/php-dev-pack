<?php
namespace TurboLabIt\TLIBaseBundle\Service\ServiceEntity;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use TurboLabIt\TLIBaseBundle\Exception\EntityLoadNotFoundException;
use TurboLabIt\TLIBaseBundle\Exception\NullInputException;
use TurboLabIt\TLIBaseBundle\Exception\UndefinedMagicMethodException;
use TurboLabIt\TLIBaseBundle\Exception\WrongTypeException;


abstract class ServiceEntity
{
    protected ServiceEntityRepository $repository;
    protected \Exception $notFoundException;

    protected $entity;
    protected array $arrData = [];
    protected bool $isSelected = false;


    public function __construct(
        protected EntityManagerInterface $em, string $entityClassName, \Exception $notFoundException = null)
    {
        $this->repository           = $this->em->getRepository($entityClassName);
        $this->entity               = new $entityClassName();
        $this->notFoundException    = empty($notFoundException) ? new EntityLoadNotFoundException() : $notFoundException;
    }


    public function loadBySlugId(string $slugId): static
    {
        $arrSlugId = $this->unpackSlugId($slugId);
        if( empty($arrSlugId) ){
            return $this->throwNotFoundException();
        }

        return $this->loadById($arrSlugId["id"]);
    }


    public function loadById(int $id): static
    {
        $this->checkNotNullInput($id);
        return $this->loadByFieldsValues(['id' => $id]);
    }


    public function loadByFieldsValues(array $arrFieldsValues): static
    {
        $this->reset();

        $entity = $this->repository->findOneBy($arrFieldsValues);
        if (empty($entity) ) {

            $this->throwNotFoundException();
        }

        return $this->setEntity($entity);
    }


    public function fakeLoadBySlugId(string $slugId): static
    {
        $arrSlugId = $this->unpackSlugId($slugId);
        if( empty($arrSlugId) ){
            return $this->throwNotFoundException();
        }

        return $this->fakeLoadById($arrSlugId["id"]);
    }


    public function fakeLoadById(int $id): static
    {
        $this
            ->checkNotNullInput($id)
            ->reset();

        $this->entity->setId($id);
        return $this;
    }


    public function setEntity($entity): static
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


    public function setData(array $arrData): static
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
        if( method_exists($this->notFoundException, 'log') ) {
            $this->notFoundException->log();
        }

        throw $this->notFoundException;
    }


    public function setSelected(bool $isIt = true): static
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


    public function checkNotNullInput($input, bool $trimValues = true): static
    {
        $arrInput = is_array($input) ? $input : [$input];
        foreach($arrInput as $item) {

            $item = is_string($item) && $trimValues ? trim($item) : $item;
            if( empty($item) ) {
                throw new NullInputException();
            }
        }

        return $this;
    }


    public function increase($field): static
    {
        $this->repository->atomicFieldIncrease($field, $this->getId());
        return $this;
    }


    public function save(bool $autoflush = true): static
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
        $dataName = strpos($name, "get") === 0 ? substr($name, 3) : $name;
        $dataName = lcfirst($dataName);
        if( array_key_exists($dataName, $this->arrData) ) {
            return $this->arrData[$dataName];
        }

        $dataName = ucfirst($dataName);
        if( array_key_exists($dataName, $this->arrData) ) {
            return $this->arrData[$dataName];
        }

        // handling a function call without "get"
        if( !method_exists($this->entity, $name) && stripos($name, 'get') !== 0 && stripos($name, 'set') === false ) {
            $name = 'get' . ucfirst($name);
        }

        // calling the method on the entity
        if( method_exists($this->entity, $name) ) {

            $result = $this->entity->$name(...$arguments);
            return $result === $this->entity ? $this : $result;
        }

        // if the method still doesn't exists on the entity => throw a specific exception to notify the developer
        throw new UndefinedMagicMethodException($this, $name);
    }
}
