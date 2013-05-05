<?php

/**
 * Builds the XHTML content for displaying a reference.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author The Laughing Man
 * @see HtmlContent
 * @see HtmlContentInterface
 */
class HtmlContentReference extends HtmlContent
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
                . "<p>Missing Reference Id.</p>"
            ."</div>"
            ."<!-- END PERSONNEL REPORT -->";
            return;
        }

		try
        {
        	//GET THE XML DATA TO BUILD THE PAGE
            $pageObjects = new PageObjectsReference;
            $pageObjects->setSearchParameters($searchParameters);
            $pageObjectsXml = $pageObjects->getData();
        }
        catch (GeneralException $e)
        {
            //DON'T DISPLAY EDIT LINK ON ERROR
            $this->source = "<input type=\"hidden\""
                          . " id=\"hidEditReferenceMenu\""
                          . " value=\"false\" />"
						  . "<div class=\"largeBlue\">"
                          . "<p>An Error Occurred in Retrieving Reference Data.</p>"
                          . "</div>";
            return;
        }

        //Set Page Title/Header
        HeaderFooter::$title = 'Reference';
        HeaderFooter::$headerDisplay = 'Reference';
        
        //If Reference exists or add privileges
        if (isset($pageObjectsXml->ReferenceList)
           || CuratorSession::checkAddPrivileges()
        )
        {
            $menu = "";
            $ownerid = $pageObjectsXml->ReferenceList->Reference->Curator->Id;

            $parentreferenceid = null;
            $memeid = null;
            $modalSource = "";
            $modal = false;
            if (get_class($this) == 'HtmlContentReferenceModal')
            {
                $modal = true;
            }
            //If page is not being opened in a modal window
            //Then display menu options.
            if (!$modal
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
                    . 'referenceid='
                    . $searchParameters->getId()
                    . '">'
                    . 'Add New Meme'
                	. '</a>'
                    . '<a class="submodal-600-525"'
                	.' href="'
                    . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                    . 'MemeListModal/'
                    . 'referenceid='
                    . $searchParameters->getId()
                    . '&searchFilter=orphaned">'
                    . 'Add Orphan Meme'
                	. '</a>'
                	. '</div>'
                	. '<div class="pagination">'
                	. '<a class="submodal-600-525"'
                	.' href="'
                    . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                    . 'ReferenceModal/'
                    . 'parentreferenceid='
                    . $searchParameters->getId()
                    . '">'
                    . 'Add New Child Reference'
                	. '</a>'
                    . '<a class="submodal-600-525"'
                	.' href="'
                    . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                    . 'ReferenceListModal/'
                    . 'parentreferenceid='
                    . $searchParameters->getId()
                    . '&searchFilter=orphaned">'
                    . 'Add Existing Child Reference'
                	. '</a>'
                	. '</div><br/>';

                JavaScript::addJavaScriptInclude("subModal");
                CascadingStyleSheets::addCascadingStyleSheetsInclude("subModal");
            }
            //If modal window
            elseif ($modal)
            {
                //If being added as a child reference.
                if (isset($_GET['parentreferenceid']))
                {
                    $parentreferenceid = $_GET['parentreferenceid'];
                    PageSession::setValue('parentreferenceid', $parentreferenceid);
                }
                elseif (PageSession::isNameSet('parentreferenceid')
                    && PageSession::getValue('parentreferenceid') != "")
                {
                    $parentreferenceid = PageSession::getValue('parentreferenceid');
                }

                //If being added to a meme.
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

                $modalSource =
            	'<script type="text/javascript">'
            	. "var clearModalSessionVariables = function(){"
            	//CLEAR MODALS REFERENCE OR SCHEMA ID ON WINDOW CLOSE
                . "parent.getContent('"
                . ROOT_FOLDER . "framework/api/setSessionVariables.php"
                . "','"
                . "application=" . Constants::getConstant('CURRENT_APPLICATION')
                . "&pageCode=ReferenceModal"
                . "&parentreferenceid&memeid','')"
                . "};"
            	. 'var closeWinAndRefreshParent = function(){'
                . 'parent.$("errorDisplaySpan").innerHTML = $("errorDisplaySpan").innerHTML;'
            	. 'clearModalSessionVariables();parent.hidePopWin(parent.ajaxGetHTML())};'
                . "setTimeout('ProcessFormSuccessObserver.subscribe(closeWinAndRefreshParent)',1000);"
                .'</script>';
            }

            $htmlViewEditContent = new HtmlViewEditContent('Reference');

            //RETRIEVE FORM LAYOUT FROM XML FILE
            $formArray = PageConfiguration::getCurrentPageForms();

            //Source to display the parent reference
            $parentReferenceSource = "";
            if ($pageObjectsXml->ParentReferenceList->ReferenceList->Reference)
            {
                $parentReferenceSource =
                    "<h3><a href=\""
                    .ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                    .'Reference/'
                    .'id='
                    .$pageObjectsXml->ParentReferenceList->ReferenceList->Reference->Id
                    ."\">"
                    ."Parent Reference</a></h3>"
                    .ReferenceHtml::getBlock(
                        $formArray
                        ,$pageObjectsXml->ParentReferenceList
                        ,null
                        ,null
                        ,null
                        ,$searchParameters->getId()
                    );
            }

            //Source to display a list of child references
            $childReferenceSource = "";
            if ($pageObjectsXml->ChildReferenceList)
            {
                foreach ($pageObjectsXml->ChildReferenceList->ReferenceList->Reference as $childReference)
                {
                    $newChildReference = new SimpleXmlObject('<Base/>');
                    $cr = $newChildReference->addChild('ReferenceList');
                    $cr->appendChild($childReference);

                    $childReferenceSource .=
                        "<hr width=\"90%\"/>"
                        ."<h3><a href=\""
                        .ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                        .'Reference/'
                        .'id='
                        .$childReference->Id
                        ."\">"
                        ."Child Reference</a></h3>"
                        .ReferenceHtml::getBlock(
                            $formArray
                            ,$newChildReference
                            ,null
                            ,null
                            ,$searchParameters->getId()
                        )
                        ."<b>Child Reference Memes</b>"
                        . MemeHtml::getList(
                            $formArray
                            ,$childReference
                            ,false
                            ,null
                            ,null
                        );
                }
            }

            if (CuratorSession::checkEditPrivileges($ownerid) || CuratorSession::checkAddPrivileges())
            {
                //LOAD EDITABLE HTML CONTENT INTO FORM
                $Form = new HtmlForm;
                //SET THE FORM CONFIGURATION
                $Form->setFormConfiguration($formArray);
                if ($parentreferenceid)
                {
                    $Form->addFormVariable('parentreferenceid',$parentreferenceid);
                }
                if ($memeid)
                {
                    $Form->addFormVariable('memeid',$memeid);
                }
                //APPEND EDITABLE TABLE INTO FORM
                $Form->appendFormContent
                (
                    ReferenceHtml::getForm($formArray,$pageObjectsXml)
                );
                //BUILD THE FORM SOURCE
                $Form->buildForm();
                if ($searchParameters->getId())
                {
                    if (!$modal)
                    {
                        $menuObject = new MenuReference;
                        $menuObject->setSource();
                        $menu =
                            '<div id="pagespecificmenu">' 
                            .$menuObject->getSource()
                            .'</div>';
                        
                        //Link to Amazon
                        $amazonLink =
                            '<link rel="stylesheet" type="text/css" href="'.ROOT_FOLDER.'framework/css/subModal.css" />'
                        	. '<div class="pagination">'
                        	. '<a class="submodal-600-525"'
                        	.' href="'
                            . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                            . 'GoogleBooksSearch/'
                            . '">'
                            . 'Link Reference to Google Books'
                        	. '</a>'
                        	. '</div><br/><br/>';
                    }

                    //View/Edit form
                    $htmlViewEditContent->setSource
                    (
                        ReferenceHtml::getBlock($formArray,$pageObjectsXml)
                        . $associateLinks
                        . "<h2>Memes</h2>"
                        . MemeHtml::getList(
                            $formArray
                            ,$pageObjectsXml
                            ,false
                            ,$searchParameters->getId()
                            ,null
                        )
                        . $childReferenceSource
                        . $parentReferenceSource
                        ,$Form->getSource().$amazonLink
                    );
                    $this->source = $htmlViewEditContent->getSource();
                }
                else
                {
                    //Set Page Title
                    HeaderFooter::$title = 'Add Reference';
                    HeaderFooter::$headerDisplay = 'Add Reference';
                    //Add form
                    if ($modalSource == "")
                    {
                        //If not modal, provide amazon autofill link
                        $modalSource =
                            '<link rel="stylesheet" type="text/css" href="'.ROOT_FOLDER.'framework/css/subModal.css" />'
                        	. '<div class="pagination">'
                        	//. '<a class="submodal-600-525"'
                        	//.' href="'
                          //  . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                          //  . 'GoogleBooksSearch/'
                          //  . '">'
                          //  . 'Search Google Books for References'
                        	//. '</a>'
                        	. '</div>';
                    }
                    $this->source = $Form->getSource().$modalSource;
                }
            }
            else
            {
                //View only page.
                $htmlViewEditContent->setSource
                (
                    ReferenceHtml::getBlock($formArray,$pageObjectsXml)
                    . "<h2>Memes</h2>"
                    . MemeHtml::getList($formArray,$pageObjectsXml)
                    . $childReferenceSource
                    . $parentReferenceSource
                );
                $this->source = $htmlViewEditContent->getSource();
            }

            //Put it all together.
            $this->source =
                $menu
                .'<div align="center">'
                .$this->source
                .'</div>';
        }
    }

}
