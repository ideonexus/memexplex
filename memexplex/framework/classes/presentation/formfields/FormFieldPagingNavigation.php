<?php

/**
 * Builds the paging navigation for clicking through list results.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormField
 * @see FormFieldInterface
 */
class FormFieldPagingNavigation extends FormField
implements FormFieldInterface
{
    protected $currentPage = 1;

    /**
     * @var integer The range of pages to show in navigation.
     */
    protected $range = 10;

    public function setRange($range = 10)
    {
        $this->range = (int)$range;
    }

    /**
     * @var integer The range of pages to show in navigation.
     */
    protected $recordsPerPage = 10;

    public function setRecordsPerPage($recordsPerPage = 10)
    {
        $this->recordsPerPage = (int) $recordsPerPage;
    }

    /**
     * @var integer The Number of pages in total.
     */
    protected $totalRecords = 0;

    public function setTotalRecords($totalRecords = 0)
    {
        $this->totalRecords = (int)$totalRecords;
    }

    /**
     * @var integer The Number of pages in total.
     */
    protected $totalPages;

    public function setTotalPages($totalPages = null)
    {
        if ($totalPages)
        {
            $this->totalPages = (int)$totalPages;
        }
    }

    /**
     * Gets total records and total pages from list object.
     *
     * @param SimpleXMLElement $formfield
     * @param SimpleXMLElement $formData
     * @param SimpleXMLElement $pageObjectsXml
     */
    public function setData
    (
        SimpleXMLElement $formfield
        ,SimpleXMLElement $formData
        ,SimpleXMLElement $pageObjectsXml
    )
    {
        if ($formfield->totalRecordsXpath)
        {
            $this->totalRecords = (int) SimpleXml::getSimpleXmlItem(
                $pageObjectsXml
                ,$formfield->totalRecordsXpath
            );
        }
        elseif ($formfield->totalPagesXpath)
        {
            $this->totalPages = (int) SimpleXml::getSimpleXmlItem(
                $pageObjectsXml
                ,$formfield->totalPagesXpath
            );

        }
    }

    /**
     * Build the source for the paging navigation.
     * $numberOfPages, $link, and $currentPage must be set
     * prior to calling this function.
     */
    public function setSource()
    {
        $this->source = "";
        If ($this->totalPages)
        {
            $numberOfPages = $this->totalPages;
        }
        else
        {
            $numberOfPages = ceil($this->totalRecords / $this->recordsPerPage);
        }

        // We need the pagination only if there are more than 1 page
        if($numberOfPages < 2)
        {
            $this->source = "";
        }
        else
        {
            
            $this->source = "<div id=\"pagingNavigation\" class=\"pagination\">";
            
            if(isset($_GET['page']))
            {
                $this->currentPage = $_GET['page'];
            }
            
            $lowerbound = $this->currentPage - $this->range;
            $upperbound = $this->currentPage + $this->range;
            
            //Add Page Numbers to the Left if within range on the right
            if ($this->currentPage > ($numberOfPages - $this->range))
            {
                $lowerbound = $lowerbound - ($this->currentPage - ($numberOfPages - $this->range));
            }
            
            //Because last lowerbound calculation could put it negative.
            if ($lowerbound < 1)
            {
                $lowerbound = 1;
            }
            
            //Add Page Number to the Right if within range on the left
            if ($lowerbound < ($this->range - $this->currentPage))
            {
                $upperbound = $upperbound + ($this->range - $this->currentPage);
            }
            
            //Because last upperbound calculation can put it over.
            if ($upperbound > $numberOfPages)
            {
                $upperbound = $numberOfPages;
            }

            if ($this->currentPage != 1)
            {
                // To the previous page
                $this->source .=
                	'<a href="javascript:void(0);"'
                	.' onclick="'
                    .'assembleQueryStringAndSubmit(\'page\',\''.($this->currentPage-1).'\')'
                	.'"'
                	.">&lt;&lt;</a>";
                
                if ($lowerbound > 1)
                {
                    $this->source .=
                    	'<a href="javascript:void(0);"'
                    	.' onclick="'
                        .'assembleQueryStringAndSubmit(\'page\',\'1\')'
                    	.'"'
                    	.">1</a>";
                    
                    if ($lowerbound > 2)
                    {
                        // Ellipses
                        $this->source .=
                            "<span>[...]</span>";
                    }
                }
            }
    
            for($i = $lowerbound; $i <= $upperbound; $i++)
            {
                $classAttribute = "";
                if ($i == $this->currentPage)
                {
                    $classAttribute = ' class="pagingCurrent"';
                }
                
                //HIDE NAVIGATION IF CURRENT PAGE NAV
                //IS GREATER THAN THE DISPLAY RANGE
                $this->source .=
                	'<a href="javascript:void(0);"'
                	.' onclick="'
                    .'assembleQueryStringAndSubmit(\'page\',\''.$i.'\')'
                	.'"'
                	.$classAttribute
                    .">$i</a>";
            }
    
            if($this->currentPage != $numberOfPages)
            {
                //DON'T DISPLAY UPPER ELLIPSES IF TOTAL
                //PAGES ARE LESS THAN THE DISPLAY RANGE
                if($upperbound < $numberOfPages)
                {
                    if ($upperbound < ($numberOfPages-1))
                    {
                        // Ellipses
                        $this->source .= 
                        	"<span>[...]</span>";
                    }
                    
                    $this->source .= 
                        	// Last page
                    	'<a href="javascript:void(0);"'
                    	.' onclick="'
                        .'assembleQueryStringAndSubmit(\'page\',\''.$numberOfPages.'\')'
                    	.'"'
                        .">$numberOfPages</a>";
                }
                $this->source .=
                	// Next page
                	'<a href="javascript:void(0);"'
                	.' onclick="'
                    .'assembleQueryStringAndSubmit(\'page\',\''.($this->currentPage+1).'\')'
                	.'"'
                	.">&gt;&gt;</a>";
                
            }
            
            $this->source .=
                "</div>";
        }
    }
}
