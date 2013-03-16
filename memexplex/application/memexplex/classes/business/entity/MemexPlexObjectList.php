<?php
/**
 * This object list was neccessary to encapsulate the TotalRows property,
 * which is used primarily for the paging navigation function in the UI
 * for determining the total number of rows in the database for a query
 * broken up into pages.
 *
 * @package MemexPlex
 * @subpackage Business.Entity
 * @see ObjectList
 * @author Douglas Ramsey
 */
abstract class MemexPlexObjectList extends ObjectList
{

    /**
     * @var integer The total number of rows in the database for a query.
     */
    protected $totalRows;

    /**
     * @param integer
     */
    public function setTotalRows($totalRows=0)
    {
        $this->totalRows = $totalRows;
    }

    /**
     * @return integer
     */
    public function getTotalRows()
    {
        return $this->totalRows;
    }

}
