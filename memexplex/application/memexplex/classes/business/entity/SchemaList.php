<?php
/**
 * @package MemexPlex
 * @subpackage Business.Entity
 * @see MemexPlexObjectList
 * @author Dr. Hans Zarkov
 */
class SchemaList extends MemexPlexObjectList
{

    /**
     * This function validates objects added
     * to the list are of the appropriate class.
     *
     * @param Schema $item
     * @return bool Whether the object was added or not
     * @throws {@link BusinessExceptionInvalidArgument}
     */
    final protected function validateItem($item)
    {
        if (!$item instanceof Schema)
        {
            throw new BusinessExceptionInvalidArgument
            (
            	'Attempt to add non Schema object to SchemaList.'
            );
            return false;
        }

        return true;
    }

    /**
     * Sort SchemaList by Title.
     *
     * @return SchemaList Sorted
     */
    public function sort()
    {
        return $this->uasort
        (
            array('Schema', 'compare')
        );
    }

}
