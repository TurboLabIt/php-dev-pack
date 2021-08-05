<?php
namespace TurboLabIt\TLIBaseBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use TurboLabIt\TLIBaseBundle\Traits\AtomicFieldIncrease;


abstract class RepositoryBase extends ServiceEntityRepository
{
    protected $rsWholeTable;


    public function loadWholeTable()
    {
        $this->rsWholeTable =
            $this->createQueryBuilderComplete()
                ->getQuery()
                ->getResult();

        return $this;
    }


    abstract function getWholeTable();


    use AtomicFieldIncrease;
}
