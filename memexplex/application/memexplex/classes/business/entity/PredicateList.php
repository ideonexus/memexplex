<?php
/**
 * @package MemexPlex
 * @subpackage Business.Entity
 * @see ObjectList
 * @author Spock
 */
class PredicateList extends ObjectList
{

    /**
     * This function validates objects added
     * to the list are of the appropriate class.
     *
     * @param Predicate $item
     * @return bool Whether the object was added or not
     * @throws {@link BusinessExceptionInvalidArgument}
     */
    final protected function validateItem($item)
    {
        if (!$item instanceof Predicate)
        {
            throw new BusinessExceptionInvalidArgument
            (
            	'Attempt to add non Predicate object to PredicateList.'
            );
            return false;
        }

        return true;
    }

    /**
     * Sort Predicates by Description
     *
     * @return this sorted
     */
    public function sort()
    {
        return $this->uasort
        (
            array('Predicate', 'compare')
        );
    }

}
