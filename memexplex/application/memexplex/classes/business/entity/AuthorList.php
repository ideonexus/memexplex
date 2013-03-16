<?php
/**
 * @package MemexPlex
 * @subpackage Business.Entity
 * @see ObjectList
 * @author Dr. Clayton Forrester
 */
class AuthorList extends ObjectList
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
        if (!$item instanceof Author)
        {
            throw new BusinessExceptionInvalidArgument
            (
            	'Attempt to add non Author object to AuthorList.'
            );
            return false;
        }

        return true;
    }

    /**
     * Sort AuthorList by last name.
     *
     * @return AuthorList Sorted
     */
    public function sort()
    {
        return $this->uasort
        (
            array('Author', 'compare')
        );
    }

}
