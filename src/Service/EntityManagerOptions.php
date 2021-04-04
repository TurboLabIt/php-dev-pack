<?php
namespace TurboLabIt\TLIBaseBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;


class EntityManagerOptions
{
    public function disableAutoincrement(EntityManagerInterface $em, array $arrClassesToDisable)
    {
        foreach($arrClassesToDisable as $className) {

            $em
                ->getClassMetadata($className)
                ->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        }

        return $this;
    }
}
