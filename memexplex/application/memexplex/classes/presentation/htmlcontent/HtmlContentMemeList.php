<?php

/**
 * Builds the XHTML content for displaying a list of memes.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Hiro Protagonist
 * @see HtmlContent
 * @see HtmlContentInterface
 */
class HtmlContentMemeList extends HtmlContent
implements HtmlContentInterface
{
    /**
     * Sets the HTML source.
     */
    public function setSource()
    {
        $searchParameters = new MemexPlexObjectSearchParameters();
        $searchParameters->setPropertiesFromGetAndPost();

        $modal = null;
        $modalSource = "";
        $referenceid = null;
        $schemaid = null;
        //Is the page being displayed in a modal window?
        if (get_class($this) == 'HtmlContentMemeListModal')
        {
            if (!Constants::getConstant('AJAX_METHOD')
                && !isset($_GET['taxonomy']))
            {
                //Unset previous session variables
                PageSession::unSetName('referenceid');
                PageSession::unSetName('schemaid');
                PageSession::unSetName('triplesearch');
            }
            
            //If modal search, confine results to curator's memes.
            ApplicationSession::setValue('DOMAIN','curator');
            $modal = true;

            //Reference Id for which memes are being searched
            if (isset($_GET['referenceid']))
            {
                $referenceid = $_GET['referenceid'];
                PageSession::setValue('referenceid', $_GET['referenceid']);
                $searchParameters->setSearchFilter('orphaned');
            }
            elseif (PageSession::isNameSet('referenceid')
                && PageSession::getValue('referenceid') != "")
            {
                $referenceid = PageSession::getValue('referenceid');
                $searchParameters->setSearchFilter('orphaned');
            }
            
            //Schema Id for which memes are being searched
            if (isset($_GET['schemaid']))
            {
                PageSession::setValue('schemaid', $_GET['schemaid']);
                $schemaid = $_GET['schemaid'];
            }
            elseif (PageSession::isNameSet('schemaid')
                && PageSession::getValue('schemaid') != "")
            {
                $schemaid = PageSession::getValue('schemaid');
            }
            
            //Triple Search for which memes are being searched
            if (isset($_GET['triplesearch']))
            {
                PageSession::setValue('triplesearch', $_GET['triplesearch']);
                $triplesearch = $_GET['triplesearch'];
            }
            elseif (PageSession::isNameSet('triplesearch')
                && PageSession::getValue('triplesearch') != "")
            {
                $triplesearch = PageSession::getValue('triplesearch');
            }
            
            //Exclude Memes already associated to reference or schema
            if (PageSession::isNameSet('referenceid')
                && PageSession::getValue('referenceid') != "")
            {
                $searchParameters->setExcludeReferenceId(PageSession::getValue('referenceid'));
            }
            elseif (PageSession::isNameSet('schemaid')
                && PageSession::getValue('schemaid') != "")
            {
                $searchParameters->setExcludeSchemaId(PageSession::getValue('schemaid'));
            }

            if ($referenceid || $schemaid)
            {
                //Close window and referesh parent window if AJAX transaction
                //to create an association.
                $modalSource =
                "var closeWinAndRefreshParent = function(){"
                . 'parent.$("errorDisplaySpan").innerHTML = $("errorDisplaySpan").innerHTML;'
                . "parent.getContent('"
                . ROOT_FOLDER . "framework/api/setSessionVariables.php"
                . "','"
                . "application=" . Constants::getConstant('CURRENT_APPLICATION')
                . "&pageCode=" . Constants::getConstant('CURRENT_PAGE_CODE')
                . "&referenceid&schemaid&triplesearch');"
                . "parent.hidePopWin(parent.ajaxGetHTML());"
                . "};"
                . "setTimeout('ProcessFormSuccessObserver.subscribe(closeWinAndRefreshParent);',200);";
            }
            else
            {
                //Clear domain, reference, and schema session variables on window close
                $modalSource =
            	'var clearModalSessionVariables = function(){'
                . "parent.getContent('"
                . ROOT_FOLDER . "framework/api/setSessionVariables.php"
                . "','"
                . "application=" . Constants::getConstant('CURRENT_APPLICATION')
                . "&pageCode=" . Constants::getConstant('CURRENT_PAGE_CODE')
                . "&referenceid&schemaid&triplesearch','');"
                . "};"
            	. "setTimeout('parent.HidePopWinObserver.subscribe(clearModalSessionVariables);',200);";
            }
        }

        try
        {
        	//GET THE XML DATA TO BUILD THE PAGE
            $pageObjects = new PageObjectsMemeList;
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
                          . "<p>An Error Occurred in Retrieving Meme List.</p>"
                          . "</div>";
            return;
        }

        //Set Page Title/Header
        if (ApplicationSession::getValue('DOMAIN') == 'curator')
        {
            HeaderFooter::$title = 'My Memes';
            HeaderFooter::$headerDisplay = 'My Memes';
        }
        elseif ($searchParameters->getCuratorId())
        {
            $curatorName = $pageObjectsXml->MemeList->Meme->Curator->DisplayName;
            HeaderFooter::$title = $curatorName . ' Memes';
            HeaderFooter::$headerDisplay = $curatorName . ' Memes';
        }
        else
        {
            HeaderFooter::$title = 'Public Memes';
            HeaderFooter::$headerDisplay = 'Public Memes';
        }
        
        //RETRIEVE FORM LAYOUT FROM XML FILE
        $formArray = PageConfiguration::getCurrentPageForms();
        //Build the page filter form source.
        $filterSource = SearchFilterHtml::getSearchFilter($formArray,$pageObjectsXml);

        $memeSource = MemeHtml::getList(
            $formArray
            ,$pageObjectsXml
            ,$modal
            ,$referenceid
            ,$schemaid
        );
        $memeSourceClass = "";
        if ($memeSource == "")
        {
            $memeSource = '<br/><p>No Memes Found</p>';
            $memeSourceClass = ' class="largeblue"';
        }
        
        //Put it all together.
        $this->source =
            $filterSource
            ."<span id=\"memelist\"$memeSourceClass>"
            .$memeSource
            ."</span>"
            ."<script type=\"text/javascript\" defer=\"defer\">"
            ."addLoadEvent(function()"
            ."{"
                // OVERIDE AJAX CONTENT TARGET
                ."setTimeout('setAjaxContentTarget(\'memelist\',\'loadingDisplay\')',500);"
            ."});"
            .$modalSource
            ."</script>";
    }

}
