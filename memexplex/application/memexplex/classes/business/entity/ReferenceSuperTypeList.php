<?php
/**
 * @package MemexPlex
 * @subpackage Business.Entity
 * @see ObjectList
 * @author Fox Mulder
 */
class ReferenceSuperTypeList extends ObjectList
{

    /**
     * This function validates objects added
     * to the list are of the appropriate class.
     *
     * @param ReferenceSuperType $item
     * @return bool Whether the object was added or not
     * @throws {@link BusinessExceptionInvalidArgument}
     */
    final protected function validateItem($item)
    {
        if (!$item instanceof ReferenceSuperType)
        {
            throw new BusinessExceptionInvalidArgument
            (
            	'Attempt to add non Reference Super Type object to ReferenceSuperTypeList.'
            );
            return false;
        }

        return true;
    }

    /**
     * Sort ReferenceSuperTypeList by description.
     *
     * @return ReferenceSuperTypeList sorted
     */
    public function sort()
    {
        return $this->uasort
        (
            array('ReferenceSuperType', 'compare')
        );
    }

}
