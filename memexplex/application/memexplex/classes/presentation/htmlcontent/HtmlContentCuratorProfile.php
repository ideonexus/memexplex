<?php

/**
 * Builds the main XHTML content for the Login page.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Dr. Bunsen Honeydew
 * @see HtmlContent
 * @see HtmlContentInterface
 */
class HtmlContentCuratorProfile extends HtmlContent
implements HtmlContentInterface
{
    /**
     * Builds the Login Form to display to the Curator
     */
    public function setSource()
    {
        $email = ApplicationSession::getValue('CURATOR_EMAIL');
        
        try
        {
        	//GET THE XML DATA TO BUILD THE PAGE
            $pageObjects = new PageObjectsCuratorProfile;
            $pageObjects->setCuratorEmail($email);
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
        HeaderFooter::$title = 'Curator Profile';
        HeaderFooter::$headerDisplay = 'Curator Profile';
        
        //RETRIEVE FORM LAYOUT FROM XML FILE
        $formArray = PageConfiguration::getCurrentPageForms();
        //BEGIN MEME TABLE CONSTRUCTION
        $FormFieldSet = new HtmlFormFieldSet("My Profile","MyProfile");
        $FormFieldSet->setFormConfiguration($formArray->CuratorProfileBlock);
        $FormFieldSet->setFormData($pageObjectsXml);
        $FormFieldSet->setPageObjectsXml($pageObjectsXml);
        //BUILDS THE VIEW/EDIT TABLES
        $FormFieldSet->appendFormFieldSet();
        //END MEME TABLE CONSTRUCTION

        //LOAD EDITABLE HTML CONTENT INTO FORM
        $Form = new HtmlForm;
        //SET THE FORM CONFIGURATION
        $Form->setFormConfiguration($formArray);
        //APPEND EDITABLE TABLE INTO FORM
        $Form->appendFormContent
        (
            $FormFieldSet->getFieldSetSource()
        );
        //BUILD THE FORM SOURCE
        $Form->buildForm();

        $this->source =
            '<div align="center">'
            .$Form->getSource()
            .'</div>';
    }
}
