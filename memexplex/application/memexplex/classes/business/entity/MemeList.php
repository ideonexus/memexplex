<?php

/**
 * @package MemexPlex
 * @subpackage Business.Entity
 * @see MemexPlexObjectList
 * @author George McFly
 */
class MemeList extends MemexPlexObjectList
{

    /**
     * This function validates objects added
     * to the list are of the appropriate class.
     *
     * @param Meme $item
     * @return bool Whether the object was added or not
     * @throws {@link BusinessExceptionInvalidArgument}
     */
    final protected function validateItem($item)
    {
        if (!$item instanceof Meme)
        {
            throw new BusinessExceptionInvalidArgument
            (
            	'Attempt to add non Meme object to MemeList.'
            );
            return false;
        }

        return true;
    }

    /**
     * Sort MemeList by Title.
     *
     * @return MemeList Sorted
     */
    public function sort()
    {
        return $this->uasort
        (
            array('Meme', 'compare')
        );
    }

}
