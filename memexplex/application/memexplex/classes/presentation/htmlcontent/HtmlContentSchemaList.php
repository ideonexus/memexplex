<?php

/**
 * Builds the XHTML content for displaying a schema list.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Martin Bishop
 * @see HtmlContent
 * @see HtmlContentInterface
 */
class HtmlContentSchemaList extends HtmlContent
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
        //Is the page being displayed in a modal window?
        if (get_class($this) == 'HtmlContentSchemaListModal')
        {
            if (!Constants::getConstant('AJAX_METHOD')
                && !isset($_GET['taxonomy']))
            {
                //Unset previous session variables
                PageSession::unSetName('memeid');
                PageSession::unSetName('schemaid');
            }
            
            //Show only the curator's references.
            ApplicationSession::setValue('DOMAIN','curator');
            $modal = true;
            //If searching for a schema to add to a meme.
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
            if (isset($_GET['parentschemaid']))
            {
                $parentSchemaId = $_GET['parentschemaid'];
                PageSession::setValue('parentschemaid', $parentSchemaId);
            }
            elseif (PageSession::isNameSet('parentschemaid')
                && PageSession::getValue('parentschemaid') != "")
            {
                $parentSchemaId = PageSession::getValue('parentschemaid');
            }
            
            if ($memeid || $parentSchemaId)
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
                . "&memeid&parentschemaid');"
                . "parent.hidePopWin(parent.ajaxGetHTML());"
                . "};"
                . "setTimeout('ProcessFormSuccessObserver.subscribe(closeWinAndRefreshParent);',200);";
            }
            
        }
        
        try
        {
        	//GET THE XML DATA TO BUILD THE PAGE
            $pageObjects = new PageObjectsSchemaList;
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
                          . "<p>An Error Occurred in Retrieving Schema Data.</p>"
                          . "</div>";
            return;
        }

        //Set Page Title/Header
        if (ApplicationSession::getValue('DOMAIN') == 'curator')
        {
            HeaderFooter::$title = 'My Schemas';
            HeaderFooter::$headerDisplay = 'My Schemas';
        }
        elseif ($searchParameters->getCuratorId())
        {
            $curatorName = $pageObjectsXml->SchemaList->Schema->Curator->DisplayName;
            HeaderFooter::$title = $curatorName . ' Schemas';
            HeaderFooter::$headerDisplay = $curatorName . ' Schemas';
        }
        else
        {
            HeaderFooter::$title = 'Public Schemas';
            HeaderFooter::$headerDisplay = 'Public Schemas';
        }
        
        //RETRIEVE FORM LAYOUT FROM XML FILE
        $formArray = PageConfiguration::getCurrentPageForms();
        //Build filter form.
        $filterSource = SearchFilterHtml::getSearchFilter($formArray,$pageObjectsXml);

        $schemaSource = SchemaHtml::getList(
            $formArray
            ,$pageObjectsXml
            ,$modal
            ,$memeid
            ,$parentSchemaId
        );
        $schemaSourceClass = "";
        if ($schemaSource == "")
        {
            $schemaSource = '<br/><p>No Schemas Found</p>';
            $schemaSourceClass = ' class="largeblue"';
        }
        
        //Put it all together.
        $this->source =
            $filterSource
            ."<span id=\"schemalist\"$schemaSourceClass>"
            .$schemaSource
            ."</span>"
            ."<script type=\"text/javascript\" defer=\"defer\">"
            ."addLoadEvent(function()"
            ."{"
                // OVERIDE AJAX CONTENT TARGET
                ."setTimeout('setAjaxContentTarget(\'schemalist\',\'loadingDisplay\')',500);"
            ."});"
            .$modalSource
            ."</script>";
    }
}
