<?php
/**
 * @package MemexPlex
 * @subpackage Business.Entity
 * @see ObjectList
 * @author Dr. Jon Osterman
 */
class TaxonomyList extends ObjectList
{

    /**
     * This function validates objects added
     * to the list are of the appropriate class.
     *
     * @param Taxonomy $item
     * @return bool Whether the object was added or not
     * @throws {@link BusinessExceptionInvalidArgument}
     */
    final protected function validateItem($item)
    {
        if (!$item instanceof Taxonomy)
        {
            throw new BusinessExceptionInvalidArgument
            (
            	'Attempt to add non Taxonomy object to TaxonomyList.'
            );
            return false;
        }

        return true;
    }

    /**
     * Sort TaxonomyList by text.
     *
     * @return TaxonomyList Sorted
     */
    public function sort()
    {
        return $this->uasort
        (
            array('Taxonomy', 'compare')
        );
    }

}
