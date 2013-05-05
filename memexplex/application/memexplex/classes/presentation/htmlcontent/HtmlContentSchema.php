<?php

/**
 * Builds the XHTML content for displaying a schema.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Thomas A. Anderson
 * @see HtmlContent
 * @see HtmlContentInterface
 */
class HtmlContentSchema extends HtmlContent
implements HtmlContentInterface
{

    /**
     * Sets the HTML source.
     */
    public function setSource()
    {

        $searchParameters = new MemexPlexObjectSearchParameters();
        $searchParameters->setPropertiesFromGetAndPost();

        if (!isset($_POST['id']) && !isset($_GET['id']) && !CuratorSession::checkAddPrivileges())
        {
            $this->source .=
            "<!-- BEGIN PERSONNEL REPORT -->"
            ."<div class=\"largeBlue\">"
                ."<p>Missing Schema Id.</p>"
            ."</div>"
            ."<!-- END PERSONNEL REPORT -->";
            return;
        }

		try
        {
        	//GET THE XML DATA TO BUILD THE PAGE
            $pageObjects = new PageObjectsSchema;
            $pageObjects->setSearchParameters($searchParameters);
            $pageObjectsXml = $pageObjects->getData($searchParameters->getId());
        }
        catch (GeneralException $e)
        {
            //DON'T DISPLAY EDIT LINK ON ERROR
            $this->source = "<input type=\"hidden\""
                          . " id=\"hidEditSchemaMenu\""
                          . " value=\"false\" />"
						  . "<div class=\"largeBlue\">"
                          . "<p>An Error Occurred in Retrieving Schema Data.</p>"
                          . "</div>";
            return;
        }

        //Set Page Title/Header
        HeaderFooter::$title = 'Schema';
        HeaderFooter::$headerDisplay = 'Schema';
        
        //If Schema to display or add privileges
        if (
             isset($pageObjectsXml->SchemaList)
             || CuratorSession::checkAddPrivileges()
        )
        {
            $menu = "";
            $ownerid = $pageObjectsXml->SchemaList->Schema->Curator->Id;

            $parentschemaid = null;
            $modalSource = "";
            $modal = false;
            if (get_class($this) == 'HtmlContentSchemaModal')
            {
                $modal = true;
            }
            
            //RETRIEVE FORM LAYOUT FROM XML FILE
            $formArray = PageConfiguration::getCurrentPageForms();

            $memeListHtml = "";
            if ($pageObjectsXml->MemeList->Meme)
            {
                $memeListHtml = 
                    "<br/><br/><h3>Memes</h3>" 
                    . MemeHtml::getList(
                        $formArray
                        ,$pageObjectsXml
                        ,false
                        ,null
                        ,$searchParameters->getId()
                    );
            }
            
            $referenceListHtml = "";
            if ($pageObjectsXml->ReferenceList->Reference)
            {
                $referenceListHtml = 
                    "<br/><br/><h3>References</h3>"
                    . ReferenceHtml::getList(
                        $formArray
                        ,$pageObjectsXml
                        ,false
                        ,null
                        ,null
                    );
            }
                
            //Build parent schema html.
            $parentSchemaSource = "";
            if ($pageObjectsXml->ParentSchemaList->SchemaList->Schema)
            {
                $parentSchemaSource =
                    "<br/><br/><h3>Parent Schemas</h3>"
                    .SchemaHtml::getList(
                        $formArray
                        ,$pageObjectsXml->ParentSchemaList
                        ,null
                        ,null
                        ,null
                    );
            }

            //Build a list of child schemas.
            $childSchemaSource = "";
            if ($pageObjectsXml->ChildSchemaList->SchemaList->Schema)
            {
                $childSchemaSource =
                    "<br/><br/><h3>Child Schemas</h3>"
                    .SchemaHtml::getList(
                        $formArray
                        ,$pageObjectsXml->ChildSchemaList
                        ,null
                        ,null
                        ,$searchParameters->getId()
                    );
            }

            //If not modal, display menu items to add relationships
            if (
                !$modal
                && $searchParameters->getId()
                && CuratorSession::checkEditPrivileges($ownerid)
            )
            {
                $associateLinks =
                    '<link rel="stylesheet" type="text/css" href="'.ROOT_FOLDER.'framework/css/subModal.css" />'
                	. '<div class="pagination">'
                	. '<a class="submodal-600-525"'
                	.' href="'
                    . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                    . 'MemeModal/'
                    . 'schemaid='
                    . $searchParameters->getId()
                    . '">'
                    . 'Add New Meme'
                	. '</a>'
                    . '<a class="submodal-600-525"'
                	.' href="'
                    . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                    . 'MemeListModal/'
                    . 'schemaid='
                    . $searchParameters->getId()
                    //. '&searchFilter=orphaned'
                    . '">'
                    . 'Add Existing Meme'
                	. '</a>'
                	. '</div>'
                	. '<div class="pagination">'
                	. '<a class="submodal-600-525"'
                	.' href="'
                    . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                    . 'SchemaModal/'
                    . 'parentschemaid='
                    . $searchParameters->getId()
                    . '">'
                    . 'Add New Child Schema'
                	. '</a>'
                    . '<a class="submodal-600-525"'
                	.' href="'
                    . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                    . 'SchemaListModal/'
                    . 'parentschemaid='
                    . $searchParameters->getId()
                    . '&searchFilter=orphaned">'
                    . 'Add Existing Child Schema'
                	. '</a>'
                	. '</div><br/>';

                JavaScript::addJavaScriptInclude("subModal");
                CascadingStyleSheets::addCascadingStyleSheetsInclude("subModal");
            }
            //If modal window
            elseif ($modal)
            {
                //Adding child schema to parent.
                if (isset($_GET['parentschemaid']))
                {
                    $parentschemaid = $_GET['parentschemaid'];
                    PageSession::setValue('parentschemaid', $parentreferenceid);
                }
                elseif (PageSession::isNameSet('parentschemaid','SchemaModal')
                    && PageSession::getValue('parentschemaid','SchemaModal') != "")
                {
                    $parentschemaid = PageSession::getValue('parentschemaid','SchemaModal');
                }

                $modalSource =
            	'<script type="text/javascript">'
            	. "var clearModalSessionVariables = function(){"
            	//CLEAR MODALS REFERENCE OR SCHEMA ID ON WINDOW CLOSE
                . "parent.getContent('"
                . ROOT_FOLDER . "framework/api/setSessionVariables.php"
                . "','"
                . "application=" . Constants::getConstant('CURRENT_APPLICATION')
                . "&pageCode=SchemaModal"
                . "&parentschemaid','')"
                . "};"
            	. "var closeWinAndRefreshParent = function(){"
                . 'parent.$("errorDisplaySpan").innerHTML = $("errorDisplaySpan").innerHTML;'
            	. "clearModalSessionVariables();parent.hidePopWin(parent.ajaxGetHTML())};"
                . "setTimeout('ProcessFormSuccessObserver.subscribe(closeWinAndRefreshParent)',1000);"
                .'</script>';
            }

            $htmlViewEditContent = new HtmlViewEditContent('Schema');

            if (CuratorSession::checkEditPrivileges($ownerid) || CuratorSession::checkAddPrivileges())
            {
                //LOAD EDITABLE HTML CONTENT INTO FORM
                $Form = new HtmlForm;
                //SET THE FORM CONFIGURATION
                $Form->setFormConfiguration($formArray);
                if ($parentschemaid)
                {
                    $Form->addFormVariable('parentschemaid',$parentschemaid);
                }
                if (isset($_GET['memeid']))
                {
                    $Form->addFormVariable('memeid',$_GET['memeid']);
                }
                //APPEND EDITABLE TABLE INTO FORM
                $Form->appendFormContent
                (
                    SchemaHtml::getForm($formArray,$pageObjectsXml)
                );
                //BUILD THE FORM SOURCE
                $Form->buildForm();
                if ($searchParameters->getId())
                {
                    if (!$modal)
                    {
                        $menuObject = new MenuSchema;
                        $menuObject->setSource();
                        $menu =
                            '<div id="pagespecificmenu">' 
                            .$menuObject->getSource()
                            .'</div>';
                    }

                    //View/Edit form
                    $htmlViewEditContent->setSource
                    (
                        SchemaHtml::getBlock($formArray,$pageObjectsXml)
                        . $associateLinks
                        . $memeListHtml
                        . $referenceListHtml
                        . $childSchemaSource
                        . $parentSchemaSource
                        ,$Form->getSource()
                    );
                    $this->source = $htmlViewEditContent->getSource();
                }
                else
                {
                    //Set Page Title
                    HeaderFooter::$title = 'Add Schema';
                    HeaderFooter::$headerDisplay = 'Add Schema';
                    //Add form.
                    $this->source = $Form->getSource().$modalSource;
                }
            }
            else
            {
                //View only.
                $htmlViewEditContent->setSource
                (
                    SchemaHtml::getBlock($formArray,$pageObjectsXml)
                    . "<h2>Memes</h2>"
                    . MemeHtml::getList($formArray,$pageObjectsXml)
                    . $referenceListHtml
                );
                $this->source = $htmlViewEditContent->getSource();
            }

            //Put it together.
            $this->source =
                $menu
                .'<div align="center">'
                .$this->source
                .'</div>';
        }
    }

}
