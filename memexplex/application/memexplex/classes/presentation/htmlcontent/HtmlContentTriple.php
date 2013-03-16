<?php

/**
 * Builds the XHTML content for displaying a triple.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author The Lone Gunmen
 * @see HtmlContent
 * @see HtmlContentInterface
 */
class HtmlContentTriple extends HtmlContent
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
                ."<p>Missing Triple Id.</p>"
            ."</div>"
            ."<!-- END PERSONNEL REPORT -->";
            return;
        }

		try
        {
        	//GET THE XML DATA TO BUILD THE PAGE
            $pageObjects = new PageObjectsTriple;
            $pageObjects->setSearchParameters($searchParameters);
            $pageObjectsXml = $pageObjects->getData($searchParameters->getId());
        }
        catch (GeneralException $e)
        {
            //DON'T DISPLAY EDIT LINK ON ERROR
            $this->source = "<input type=\"hidden\""
                          . " id=\"hidEditTripleMenu\""
                          . " value=\"false\" />"
						  . "<div class=\"largeBlue\">"
                          . "<p>An Error Occurred in Retrieving Triple Data.</p>"
                          . "</div>";
            return;
        }

        //Set Page Title/Header
        HeaderFooter::$title = 'Triple';
        HeaderFooter::$headerDisplay = 'Triple';
        
        //If Triple exists or add privileges.
        if (
             isset($pageObjectsXml->TripleList)
             || CuratorSession::checkAddPrivileges()
        )
        {
            $menu = "";
            $ownerid = $pageObjectsXml->TripleList->Triple->Curator->Id;

            $parenttripleid = null;
            $modalSource = "";
            
            $predicateDescription = "";
            if ($pageObjectsXml->TripleList->Triple->Predicate->Description)
            {
                $predicateDescription = $pageObjectsXml->TripleList->Triple->Predicate->Description;
            }
                
            $subjectMemeXml = new SimpleXmlObject("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<base></base>");
            $objectMemeXml = new SimpleXmlObject("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<base></base>");
            
            if (isset($pageObjectsXml->TripleList))
            {
                $subjectMemeList = $subjectMemeXml->addChild('MemeList');
                $subjectMemeList->appendChild($pageObjectsXml->TripleList->Triple->Subject->Meme);
                
                $objectMemeList = $objectMemeXml->addChild('MemeList');
                $objectMemeList->appendChild($pageObjectsXml->TripleList->Triple->Object->Meme);
            }

            $htmlViewEditContent = new HtmlViewEditContent('Triple');

            //RETRIEVE FORM LAYOUT FROM XML FILE
            $formArray = PageConfiguration::getCurrentPageForms();

            if (CuratorSession::checkEditPrivileges($ownerid) || CuratorSession::checkAddPrivileges())
            {
                //LOAD EDITABLE HTML CONTENT INTO FORM
                $Form = new HtmlForm;
                //SET THE FORM CONFIGURATION
                $Form->setFormConfiguration($formArray);
                //APPEND EDITABLE TABLE INTO FORM
                $Form->appendFormContent
                (
                    TripleHtml::getForm($formArray,$pageObjectsXml)
                    .'<div id="subject" class="triplesubjectobject">'
                    .MemeHtml::getBlock($formArray,$subjectMemeXml,false,$searchParameters->getId())
                    .'</div>'
                    .'<div class="modaltripledivlistitem" style="text-align:center;">'
                    . '<a class="submodal-600-525"'
                	.' style="display:none;"'
                	.' href="'
                    . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                    . 'MemeListModal/'
                    . 'triplesearch=subject'
                    . '">'
                    . 'Add New Subject Meme'
                	. '</a>'
                    .'<div id="predicateDisplay" class="predicatedisplay">'
                    .$predicateDescription
                    .'</div>'
                    . '<a class="submodal-600-525"'
                	.' style="display:none;"'
                	.' href="'
                    . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                    . 'MemeListModal/'
                    . 'triplesearch=object'
                    . '">'
                    . 'Add New Object Meme'
                	. '</a>'
                	.'</div>'
                    .'<div id="object" class="triplesubjectobject">'
                    .MemeHtml::getBlock($formArray,$objectMemeXml,false,$searchParameters->getId())
                    .'</div>'
                );
                
                //BUILD THE FORM SOURCE
                $Form->buildForm();
                if ($searchParameters->getId())
                {
                    $menuObject = new MenuTriple;
                    $menuObject->setSource();
                    $menu =
                        '<div id="pagespecificmenu">' 
                        .$menuObject->getSource()
                        .'</div>';
                    
                    //View/Edit Form
                    $htmlViewEditContent->setSource
                    (
                        TripleHtml::getBlock($formArray,$pageObjectsXml)
                        .MemeHtml::getBlock($formArray,$subjectMemeXml,null,$searchParameters->getId(),true)
                        .'<div class="predicatedisplay">'
                        .$predicateDescription
                        .'</div>'
                        .MemeHtml::getBlock($formArray,$objectMemeXml,null,$searchParameters->getId(),true)
                        ,$Form->getSource()
                    );
                    $this->source = $htmlViewEditContent->getSource();
                }
                else
                {
                    //Set Page Title/Header
                    HeaderFooter::$title = 'Add Triple';
                    HeaderFooter::$headerDisplay = 'Add Triple';
                    //Add Form.
                    $this->source = $Form->getSource();
                }
            }
            else
            {
                //View Only
                $htmlViewEditContent->setSource
                (
                    TripleHtml::getBlock($formArray,$pageObjectsXml)
                    .MemeHtml::getBlock($formArray,$subjectMemeXml,null,$searchParameters->getId())
                    .'<div class="predicatedisplay">'
                    .$predicateDescription
                    .'</div>'
                    .MemeHtml::getBlock($formArray,$objectMemeXml,null,$searchParameters->getId())
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
