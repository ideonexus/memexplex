<?php
/**
 * File containing the FooBarList class definition
 *
 * @package MemexPlex
 * @subpackage Business.Entity
 * @see ObjectList
 * @author Xenu
 */
class ReferenceTypeList extends ObjectList
{

    /**
     * This function validates objects added
     * to the list are of the appropriate class.
     *
     * @param ReferenceType $item
     * @return bool Whether the object was added or not
     * @throws {@link BusinessExceptionInvalidArgument}
     */
    final protected function validateItem($item)
    {
        if (!$item instanceof ReferenceType)
        {
            throw new BusinessExceptionInvalidArgument
            (
            	'Attempt to add non Reference Type object to ReferenceTypeList.'
            );
            return false;
        }

        return true;
    }

    /**
     * Sort ReferenceTypeList by description.
     *
     * @return ReferenceTypeList sorted
     */
    public function sort()
    {
        return $this->uasort
        (
            array('ReferenceType', 'compare')
        );
    }

}
