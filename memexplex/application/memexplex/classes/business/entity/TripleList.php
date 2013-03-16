<?php
/**
 * @package MemexPlex
 * @subpackage Business.Entity
 * @see MemexPlexObjectList
 * @author Hari Seldon
 */
class TripleList extends MemexPlexObjectList
{

    /**
     * This function validates objects added
     * to the list are of the appropriate class.
     *
     * @param Triple $item
     * @return bool Whether the object was added or not
     * @throws {@link BusinessExceptionInvalidArgument}
     */
    final protected function validateItem($item)
    {
        if (!$item instanceof Triple)
        {
            throw new BusinessExceptionInvalidArgument
            (
            	'Attempt to add non Triple object to TripleList.'
            );
            return false;
        }

        return true;
    }

    /**
     * Sort TripleList by Title.
     *
     * @return TripleList Sorted
     */
    public function sort()
    {
        return $this->uasort
        (
            array('Triple', 'compare')
        );
    }

}
