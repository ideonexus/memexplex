<?php

/**
 * Builds the XHTML to display a specific Meme to the Curator.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Randy Waterhouse
 * @see HtmlContent
 * @see HtmlContentInterface
 */
class HtmlContentMeme extends HtmlContent
implements HtmlContentInterface
{

    /**
     * Displays a Meme to the Curator.
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
                . "<p>Missing Meme Id.</p>"
            ."</div>"
            ."<!-- END PERSONNEL REPORT -->";
            return;
        }

		try
        {
        	//GET THE XML DATA TO BUILD THE PAGE
            $pageObjects = new PageObjectsMeme;
            $pageObjects->setSearchParameters($searchParameters);
            $pageObjectsXml = $pageObjects->getData();
        }
        catch (GeneralException $e)
        {
            //DON'T DISPLAY EDIT LINK ON ERROR
            $this->source = "<input type=\"hidden\""
                          . " id=\"hidEditMemeMenu\""
                          . " value=\"false\" />"
						  . "<div class=\"largeBlue\">"
                          . "<p>An Error Occurred in Retrieving Meme Data.</p>"
                          . "</div>";
            return;
        }

        //Set Page Title/Header
        HeaderFooter::$title = 'Meme';
        HeaderFooter::$headerDisplay = 'Meme';
        
        //If Meme exists or user has add privileges.
        if (
             isset($pageObjectsXml->MemeList->Meme)
             ||  CuratorSession::checkAddPrivileges()
        )
        {
            $menu = "";
            $ownerid = $pageObjectsXml->MemeList->Meme->Curator->Id;

            $modalFormSource = "";
            $modal = false;

            //Check if this is being displayed as a Modal Window.
            if (get_class($this) == 'HtmlContentMemeModal')
            {
                $modal = true;
            }
                
            //RETRIEVE FORM LAYOUT FROM XML FILE
            $formArray = PageConfiguration::getCurrentPageForms();

            $referenceHtml = "";
            $tripleHtml = "";
            $schemaHtml = "";
            $associateLinks = "";
            if (!$modal)
            {
                if (isset($pageObjectsXml->ReferenceList->Reference->Id))
                {
                    $referenceHtml = 
                        ReferenceHtml::getBlock(
                            $formArray
                            ,$pageObjectsXml
                            ,$searchParameters->getId()
                            ,$modal
                        );
                }
                
                if (CuratorSession::checkEditPrivileges($ownerid))
                {
                    $associateLinks =
                        '<link rel="stylesheet" type="text/css" href="'.ROOT_FOLDER.'framework/css/subModal.css" />'
                        . '<div class="pagination">';
                    if ($referenceHtml == "")
                    {
                        $associateLinks .=
                        	'<a class="submodal-600-525"'
                        	.' style="display:none;"'
                        	.' href="'
                            . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                            . 'ReferenceModal/'
                            . 'memeid='
                            . $searchParameters->getId()
                            . '">'
                            . 'Add New Reference'
                        	. '</a>'
                            . '<a class="submodal-600-525"'
                            .' style="display:none;"'
                            .' href="'
                            . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                            . 'ReferenceListModal/'
                            . 'memeid='
                            . $searchParameters->getId()
                            . '">'
                            . 'Add Existing Reference'
                        	. '</a>';
                    }
                    
                    $associateLinks .=
                    	'<a class="submodal-600-525"'
                    	.' style="display:none;"'
                    	.' href="'
                        . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                        . 'SchemaModal/'
                        . 'memeid='
                        . $searchParameters->getId()
                        . '">'
                        . 'Add to New Schema'
                    	. '</a>'
                        . '<a class="submodal-600-525"'
                    	.' style="display:none;"'
                    	.' href="'
                        . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                        . 'SchemaListModal/'
                        . 'memeid='
                        . $searchParameters->getId()
                        . '">'
                        . 'Add to Existing Schema'
                    	. '</a>'
                        .'</div><br/>';
                        
                    JavaScript::addJavaScriptInclude("subModal");
                    CascadingStyleSheets::addCascadingStyleSheetsInclude("subModal");
                }
                
                if (isset($pageObjectsXml->TripleList->Triple))
                {
                    $tripleHtml =
                        "<br/><br/><h3>Triples</h3>" 
                        .TripleHtml::getList(
                            $formArray
                            ,$pageObjectsXml
                        );
                }
                
                if (isset($pageObjectsXml->SchemaList->Schema))
                {
                    $id = null;
                    if (CuratorSession::checkEditPrivileges($ownerid))
                    {
                        $id = $searchParameters->getId();
                    }
                    
                    $schemaHtml =
                        "<br/><br/><h3>Schemas</h3>" 
                        .SchemaHtml::getList(
                            $formArray
                            ,$pageObjectsXml
                            ,$modal
                            ,$id
                        );
                }
            }
            
            $htmlViewEditContent = new HtmlViewEditContent('Meme');

            //BUILD MEME DISPLAY
            $MemeDisplay = "";
            $modalSource = "";

            //Build Form
            if (CuratorSession::checkEditPrivileges($ownerid) || CuratorSession::checkAddPrivileges())
            {
                if ($modal)
                {
                    //IF EDIT AND MODAL, DON'T BUILD FORM, BUT
                    //PRESENT ADD TO SCHEMA/REFERENCE LINK
                    if ($searchParameters->getId())
                    {
                        $linkVars = null;
                        $linkText = "";
                        //Check for Modal Window to add meme to Reference.
                        if (PageSession::isNameSet('referenceid','MemeListModal')
                            && PageSession::getValue('referenceid','MemeListModal') != "")
                        {
                            $linkVars = 'referenceid='.PageSession::getValue('referenceid','MemeListModal');
                            $linkText = "Reference";
                        }
                        //Check for Modal Window to add meme to Schema.
                        elseif (PageSession::isNameSet('schemaid','MemeListModal')
                            && PageSession::getValue('schemaid','MemeListModal') != "")
                        {
                            $linkVars = 'schemaid='.PageSession::getValue('schemaid','MemeListModal');
                            $linkText = "Schema";
                        }
                        //Check for Modal Window to add meme to Triple.
                        elseif (PageSession::isNameSet('triplesearch','MemeListModal')
                            && PageSession::getValue('triplesearch','MemeListModal') != "")
                        {
                            $linkVars = PageSession::getValue('triplesearch','MemeListModal');
                            $linkText = "Triple";
                        }

                        if ($linkVars)
                        {
                            $modalFormSource .=
                                '<div class="pagination">' 
                            	."<p><a href=\"" . ROOT_FOLDER . "MemeListModal/\">Return to MemeList</a>";
                            if ($linkText == "Triple")
                            {
                                $modalFormSource .=
                                	"<a href=\"javascript:void(0);\""
                                    ." onclick=\"populateTriple("
                                    ."'".$linkVars."','".$searchParameters->getId()."');\""
                                    .">Add Meme to Triple</a>";
                                JavaScript::addJavaScriptInclude("TripleSearch");
                            }
                            else
                            {
                                //Link to make AJAX call to associate meme to reference or schema
                                $modalFormSource .=
                                    "<a href=\"javascript:void(0);\" "
                                    ."onClick=\""
                                	."getContent('"
                                	. ROOT_FOLDER . "framework/api/processForm.php"
                                	."','"
                                    . "application="
                                    . Constants::getConstant('CURRENT_APPLICATION')
                                    . "&pageCode=ParentChild"
                                    . "&function=associate"
                                    . "&" . $linkVars
                                    . "&memeid=" . $searchParameters->getId()
                                	."','processFormCallback'"
                                	.");parent.ajaxGetHTML();"
                                    . "\">Add Meme to " . $linkText . "</a>";
                                JavaScript::addJavaScriptInclude("ajaxProcessForm");
                            }
                            $modalFormSource .= "</div><br/>";
                        }
                    }

                    $modalSource =
                	'<script type="text/javascript">'
                	. "var clearModalSessionVariables = function(){"
                	//CLEAR MODALS REFERENCE OR SCHEMA ID ON WINDOW CLOSE
                    . "parent.getContent('"
                    . ROOT_FOLDER . "framework/api/setSessionVariables.php"
                    . "','"
                    . "application=" . Constants::getConstant('CURRENT_APPLICATION')
                    . "&pageCode=MemeListModal"
                    . "&referenceid&schemaid','');"
                    . "};"
                	. "setTimeout('parent.HidePopWinObserver.subscribe(clearModalSessionVariables);',1000);"
                    .'</script>';
                }

                //IF NOT MODAL OR IS ADD PAGE, BUILD FORM
                if (!$modal || !$searchParameters->getId())
                {
                    if ($modal)
                    {
                        //Close Window and Refresh parent window on successful AJAX transaction
                        $modalSource =
                    	'<script type="text/javascript">'
                    	.'var closeWinAndRefreshParent = function(){'
                        . 'parent.$("errorDisplaySpan").innerHTML = $("errorDisplaySpan").innerHTML;'
                    	.'parent.hidePopWin(parent.ajaxGetHTML())};'
                        .'setTimeout(\'ProcessFormSuccessObserver.subscribe(closeWinAndRefreshParent);\',1000);'
                        .'</script>';
                    }

                    //LOAD EDITABLE HTML CONTENT INTO FORM
                    $Form = new HtmlForm;
                    //SET THE FORM CONFIGURATION
                    $Form->setFormConfiguration($formArray);
                    //Add Reference Id as hidden variable to the form if it exists.
                    if (PageSession::isNameSet('referenceid','MemeListModal')
                        && PageSession::getValue('referenceid','MemeListModal') != "")
                    {
                        $Form->addFormVariable('referenceid',PageSession::getValue('referenceid','MemeListModal'));
                    }
                    //Add Schema Id as hidden variable to the form if it exists.
                    if (PageSession::isNameSet('schemaid','MemeListModal')
                        && PageSession::getValue('schemaid','MemeListModal') != "")
                    {
                        $Form->addFormVariable('schemaid',PageSession::getValue('schemaid','MemeListModal'));
                    }
                    //APPEND EDITABLE TABLE INTO FORM
                    $Form->appendFormContent
                    (
                        MemeHtml::getForm($formArray,$pageObjectsXml)
                    );
                    //BUILD THE FORM SOURCE
                    $Form->buildForm();

                    if ($searchParameters->getId())
                    {
                        if (!$modal)
                        {
                            $menuObject = new MenuMeme;
                            $menuObject->setSource();
                            $menu =
                                '<div id="pagespecificmenu">' 
                                .$menuObject->getSource()
                                .'</div>';
                        }

                        //Build Meme, Reference, and Edit Form
                        $htmlViewEditContent->setSource
                        (
                            MemeHtml::getBlock($formArray,$pageObjectsXml,$modal)
                            . $associateLinks
                            . $referenceHtml
                            . $tripleHtml
                            . $schemaHtml
                            ,$Form->getSource()
                        );
                        $this->source = $htmlViewEditContent->getSource();
                    }
                    else
                    {
                        //Set Page Title/Header
                        HeaderFooter::$title = 'Add Meme';
                        HeaderFooter::$headerDisplay = 'Add Meme';
                        //Build add form.
                        $this->source = $Form->getSource().$modalSource;
                    }
                }
                else
                {
                    //View Only Page Modal
                    $htmlViewEditContent->setSource
                    (
                        MemeHtml::getBlock($formArray,$pageObjectsXml,$modal)
                    );
                    $this->source = $htmlViewEditContent->getSource();
                }
            }
            else
            {
                //View only page.
                $htmlViewEditContent->setSource
                (
                    MemeHtml::getBlock($formArray,$pageObjectsXml)
                    . $referenceHtml
                    . $tripleHtml
                    . $schemaHtml
                );
                $this->source = $htmlViewEditContent->getSource();
            }

            //Complete page display.
            $this->source =
                $menu
                .'<div align="center">'
                .$modalFormSource
                .$this->source . $this->externalSource
                .$modalFormSource
                .'</div>';
        }
    }

}
