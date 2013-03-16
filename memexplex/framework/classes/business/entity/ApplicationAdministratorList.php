<?php

/**
 * List object for Application Administrators.
 *
 * @package Framework
 * @subpackage Business.Entity
 * @author Ryan Somma
 */
class ApplicationAdministratorList extends ObjectList
{

    /**
     * Validates objects added to list are appadmins
     *
     * @param ApplicationAdministrator $item
     * @return bool
     * @throws {@link EntityExceptionInvalidArgument}
     */
    final protected function validateItem($item)
    {

        if (!$item instanceof ApplicationAdministrator)
        {
            throw new EntityExceptionInvalidArgument();
            return false;
        }

        return true;
    }

    /**
     * Gets a semicolon-separated email list.
     *
     * @return string
     */
    public function getEmailList()
    {
        $emailList = '';
        $list = $this->getArrayCopy();
        foreach ($list as $item)
        {
            $emailList .= $item->getEmail() . ";";
        }
        return $emailList;
    }

}
