<?php
namespace TurboLabIt\TLIBaseBundle\Repository;


abstract class RepositoryBaseCompositeKey extends RepositoryBase
{
    protected array $arrWholeTableCompositeIndexFastLookup = [];


    public function loadWholeTable()
    {
        parent::loadWholeTable();

        foreach($this->rsWholeTable as $entity) {

            $this->addNewEntryToCompositeIndex($entity);
        }

        return $this;
    }


    public function getWholeTable()
    {
        return $this->arrWholeTableCompositeIndexFastLookup;
    }


    protected function addNewEntryToCompositeIndex($entity)
    {
        $idx = $this->buildCompositeIndexFastLookup($entity);
        $this->arrWholeTableCompositeIndexFastLookup[$idx] = $entity;
        return $entity;
    }


    abstract protected function buildCompositeIndexFastLookup($entity): string;


    protected function joinValues(...$arrValues)
    {
        return implode('|', $arrValues);
    }


    public function selectOrNull(...$arrEntities)
    {
        $arrIds = [];
        foreach($arrEntities as $entity) {

            $id = $entity->getId();
            if(empty($id)) {

                return null;
            }

            $arrIds[] = $id;
        }

        $idx = $this->joinValues(...$arrIds);

        return
            in_array($idx, $this->arrWholeTableCompositeIndexFastLookup)
                ? $this->arrWholeTableCompositeIndexFastLookup[$idx]
                : null
            ;
    }
}
