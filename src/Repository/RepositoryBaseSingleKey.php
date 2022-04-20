<?php
namespace TurboLabIt\TLIBaseBundle\Repository;


abstract class RepositoryBaseSingleKey extends RepositoryBase
{
    public function getWholeTable()
    {
        return $this->rsWholeTable;
    }


    public function getOneById(int $id)
    {
        return
            $this->createQueryBuilderComplete()
                ->andWhere('t.id = :id')
                    ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
    }


    public function getByIds(array $arrIds)
    {
        return
            $this->createQueryBuilderComplete()
                ->andWhere('t.id IN(:ids)')
                    ->setParameter('ids', $arrIds)
                ->getQuery()
                ->getResult();
    }


    public function selectOrNew(?int $id)
    {
        $entity = $this->selectOrNull($id);
        if( !empty($entity) ) {
            return $entity;
        }

        $newEntity = new $this->_entityName();

        if( !empty($id) && method_exists($entity, 'setId') ) {

            $newEntity->setId($id);
            $this->rsWholeTable[$id] = $newEntity;
        }

        return $newEntity;
    }


    public function selectOrNull(?int $id)
    {
        if( empty($id) || !array_key_exists($id, $this->rsWholeTable) ) {
            return null;
        }

        return $this->rsWholeTable[$id];
    }
}
