<?php

/**
 * Builds the XHTML content for displaying a reference list.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Boris Grishenko
 * @see HtmlContent
 * @see HtmlContentInterface
 */
class HtmlContentReferenceList extends HtmlContent
implements HtmlContentInterface
{

    /**
     * Sets the HTML source.
     */
    public function setSource()
    {

        $modal = null;
        $memeid = null;
        $schemaid = null;
        $parentReferenceId = null;
        $modalSource = "";
        //If page is being rendered in a modal window.
        if (get_class($this) == 'HtmlContentReferenceListModal')
        {
            if (!Constants::getConstant('AJAX_METHOD')
                && !isset($_GET['taxonomy']))
            {
                //Unset previous session variables
                PageSession::unSetName('parentreferenceid');
                PageSession::unSetName('memeid');
            }
                        
            //Show only the curator's references.
            ApplicationSession::setValue('DOMAIN','curator');
            $modal = true;
            
            //If searching for a reference to add to a meme.
            if (isset($_GET['memeid']))
            {
                $memeid = $_GET['memeid'];
                PageSession::setValue('memeid', $memeid);
            }
            elseif (PageSession::isNameSet('memeid')
                && PageSession::getValue('memeid') != "")
            {
                $memeid = PageSession::getValue('memeid');
            }

            //If searching for a reference to add as a child to a reference
            if (isset($_GET['parentreferenceid']))
            {
                $parentReferenceId = $_GET['parentreferenceid'];
                PageSession::setValue('parentreferenceid', $parentReferenceId);
            }
            elseif (PageSession::isNameSet('parentreferenceid')
                && PageSession::getValue('parentreferenceid') != "")
            {
                $parentReferenceId = PageSession::getValue('parentreferenceid');
            }

            if ($memeid || $parentReferenceId)
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
                . "&memeid&parentreferenceid');"
                . "parent.hidePopWin(parent.ajaxGetHTML());"
                . "};"
                . "setTimeout('ProcessFormSuccessObserver.subscribe(closeWinAndRefreshParent);',200);";
            }

        }

        $searchParameters = new MemexPlexObjectSearchParameters();
        $searchParameters->setPropertiesFromGetAndPost();

        try
        {
        	//GET THE XML DATA TO BUILD THE PAGE
            $pageObjects = new PageObjectsReferenceList;
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
                          . "<p>An Error Occurred in Retrieving Reference Data.</p>"
                          . "</div>";
            return;
        }

        //Set Page Title/Header
        if (ApplicationSession::getValue('DOMAIN') == 'curator')
        {
            HeaderFooter::$title = 'My References';
            HeaderFooter::$headerDisplay = 'My References';
        }
        elseif ($searchParameters->getCuratorId())
        {
            $curatorName = $pageObjectsXml->ReferenceList->Reference->Curator->DisplayName;
            HeaderFooter::$title = $curatorName . ' References';
            HeaderFooter::$headerDisplay = $curatorName . ' References';
        }
        else
        {
            HeaderFooter::$title = 'Public References';
            HeaderFooter::$headerDisplay = 'Public References';
        }
        
        //RETRIEVE FORM LAYOUT FROM XML FILE
        $formArray = PageConfiguration::getCurrentPageForms();
        //Build the form filters for the page.
        $filterSource = SearchFilterHtml::getSearchFilter($formArray,$pageObjectsXml);

        $referenceSource = ReferenceHtml::getList(
            $formArray
            ,$pageObjectsXml
            ,$modal
            ,$memeid
            ,$parentReferenceId
        );
        $referenceSourceClass = "";
        if ($referenceSource == "")
        {
            $referenceSource = '<br/><p>No References Found</p>';
            $referenceSourceClass = ' class="largeblue"';
        }
            
        //Put it all together.
        $this->source =
            $filterSource
            ."<span id=\"referencelist\"$referenceSourceClass>"
            .$referenceSource
            ."</span>"
            ."<script type=\"text/javascript\" defer=\"defer\">"
            ."addLoadEvent(function()"
            ."{"
            // OVERIDE AJAX CONTENT TARGET
                ."setTimeout(\"setAjaxContentTarget('referencelist','loadingDisplay')\",200);"
            ."});"
            .$modalSource
            ."</script>";
    }

}
