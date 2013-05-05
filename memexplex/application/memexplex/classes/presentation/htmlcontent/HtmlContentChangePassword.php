<?php

/**
 * Builds the XHTML content for displaying the change password form.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Krank
 * @see HtmlContent
 * @see HtmlContentInterface
 */
class HtmlContentChangePassword extends HtmlContent
implements HtmlContentInterface
{

    /**
     * Sets the HTML source.
     */
    public function setSource()
    {
        //Set Page Title/Header
        HeaderFooter::$title = 'Change Password';
        HeaderFooter::$headerDisplay = 'Change Password';
        
        $pageObjectsXml = new SimpleXmlObject('<base/>');
        $displayForm = true;
        if (!ApplicationSession::isNameSet('CURATOR_ID'))
        {
            if (isset($_GET['uid']))
            {
                $pageObjectsXml->LoggedIn = 'false';
            }
            else
            {
                $displayForm = false;
            }
        }

        if ($displayForm)
        {
            //RETRIEVE FORM LAYOUT FROM XML FILE
            $formArray = PageConfiguration::getCurrentPageForms();
            //BEGIN MEME TABLE CONSTRUCTION
            $FormFieldSet = new HtmlFormFieldSet("Change Password","ChangePassword");
            $emptyData = new SimpleXMLElement("<base></base>");
            $FormFieldSet->setFormConfiguration($formArray->ChangePasswordBlock);
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
        else
        {
            $this->source =
                '<div align="center">'
                .'<br/><br/>'
                .'<p class="largeBlue">'
                .'You must be logged in or visiting this page<br/>from an emailed link for it to display.'
                .'</p>'
                .'</div>';
        }
    }

}
