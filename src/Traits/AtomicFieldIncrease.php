<?php
namespace TurboLabIt\TLIBaseBundle\Traits;


trait AtomicFieldIncrease
{
    public function atomicFieldIncrease(string $fieldToIncrease, int $entityId, int $incrementBy = 1)
    {
        $em         = $this->getEntityManager();
        $db         = $em->getConnection();
        $tableName  = $em->getClassMetadata($this->getClassName())->getTableName();

        $fieldToIncrease = preg_replace("/['\"`]/is", '', $fieldToIncrease);

        $sql = '
            UPDATE
                `' . $tableName .'` 
            SET ' .
                $fieldToIncrease . " = " . $fieldToIncrease . ' + ' . $incrementBy . '
            WHERE
                id = ' . $entityId
        ;

        $stmt = $db->prepare($sql);
        $stmt->executeStatement();

        return $this;
    }
}
