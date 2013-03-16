<?php
/**
 * @package MemexPlex
 * @subpackage Business.Entity
 * @see MemexPlexObjectList
 * @author Dr. Victor Frankenstein
 */
class ReferenceList extends MemexPlexObjectList
{

    /**
     * This function validates objects added
     * to the list are of the appropriate class.
     *
     * @param Reference $item
     * @return bool Whether the object was added or not
     * @throws {@link BusinessExceptionInvalidArgument}
     */
    final protected function validateItem($item)
    {
        if (!$item instanceof Reference)
        {
            throw new BusinessExceptionInvalidArgument
            (
            	'Attempt to add non Reference object to ReferenceList.'
            );
            return false;
        }

        return true;
    }

    /**
     * Sort ReferenceList by Title.
     *
     * @return this sorted
     */
    public function sort()
    {
        return $this->uasort
        (
            array('Reference', 'compare')
        );
    }

}
