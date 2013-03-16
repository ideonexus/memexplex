<?php

/**
 * Properties and Methods for all Html Objects.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see Html
 */
abstract class HtmlContent extends Html
{

    /**
     * Filters for the content.
     * @var array
     */
    protected $filterArray = array();

    /**
     * Current pagecode.
     * @var string
     */
    protected $pageCode;

    /**
     * @param string $pageCode
     */
    public function __construct($pageCode)
    {
        $this->pageCode = $pageCode;
    }

    /**
     * @param array $filterArray
     */
    public function setFilterArray($filterArray = array())
    {
        $this->filterArray = $filterArray;
    }

}
