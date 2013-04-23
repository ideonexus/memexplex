<?php

/**
 * Builds the XHTML content for displaying a triple list.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Ender Wiggin
 * @see HtmlContent
 * @see HtmlContentInterface
 */
class HtmlContentTripleList extends HtmlContent
implements HtmlContentInterface
{
    /**
     * Sets the HTML source.
     */
    public function setSource()
    {
        $searchParameters = new MemexPlexObjectSearchParameters();
        $searchParameters->setPropertiesFromGetAndPost();

        try
        {
        	//GET THE XML DATA TO BUILD THE PAGE
            $pageObjects = new PageObjectsTripleList;
            $pageObjects->setSearchParameters($searchParameters);
            $pageObjectsXml = $pageObjects->getData();
        }
        catch (GeneralException $e)
        {
            //DON'T DISPLAY EDIT LINK ON ERROR
            $this->source = "<input type=\"hidden\""
                          . " id=\"hidEditFooBarsMenu\""
                          . " value=\"false\" />"
						  . "<div class=\"largeBlue\">"
                          . "<p>An Error Occurred in Retrieving Triple Data.</p>"
                          . "</div>";
            return;
        }

        //Set Page Title/Header
        if (ApplicationSession::getValue('DOMAIN') == 'curator')
        {
            HeaderFooter::$title = 'My Triples';
            HeaderFooter::$headerDisplay = 'My Triples';
        }
        elseif ($searchParameters->getCuratorId())
        {
            $curatorName = $pageObjectsXml->TripleList->Triple->Curator->DisplayName;
            HeaderFooter::$title = $curatorName . ' Triples';
            HeaderFooter::$headerDisplay = $curatorName . ' Triples';
        }
        else
        {
            HeaderFooter::$title = 'Public Triples';
            HeaderFooter::$headerDisplay = 'Public Triples';
        }
        
        //RETRIEVE FORM LAYOUT FROM XML FILE
        $formArray = PageConfiguration::getCurrentPageForms();
        //Build filter form.
        $filterSource = SearchFilterHtml::getSearchFilter($formArray,$pageObjectsXml);

        $tripleSource = TripleHtml::getList($formArray,$pageObjectsXml);
        $tripleSourceClass = "";
        if ($tripleSource == "")
        {
            $tripleSource = '<br/><p>No Triples Found</p>';
            $tripleSourceClass = ' class="largeblue"';
        }
        
        //Bring it all together.
        $this->source =
            $filterSource
            . '<span id="triplelist"'.$tripleSourceClass.'>'
            .$tripleSource
            . '</span>'
            . '<script type="text/javascript" defer="defer">'
            . 'addLoadEvent(function()'
            . '{'
                // OVERIDE AJAX CONTENT TARGET
                ."setTimeout('setAjaxContentTarget(\'triplelist\',\'loadingDisplay\')',500);"
            . '});'
            . '</script>';
    }
}
