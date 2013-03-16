<?php
/**
 * @package MemexPlex
 * @subpackage Business.Entity
 * @see ObjectList
 * @author McLovin
 */
class DeltaList extends ObjectList
{

    /**
     * This function validates objects added
     * to the list are of the appropriate class.
     *
     * @param Delta $item
     * @return bool Whether the object was added or not
     * @throws {@link BusinessExceptionInvalidArgument}
     */
    final protected function validateItem($item)
    {

        if (!$item instanceof Delta)
        {
            throw new BusinessExceptionInvalidArgument
            (
                'Invalid Delta Object submitted to DeltaList.'
            );

            return false;
        }

        return true;
    }

}
