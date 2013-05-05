<?php

/**
 * Builds the main XHTML content for the Login page.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Noah Kuttler
 * @see HtmlContent
 * @see HtmlContentInterface
 */
class HtmlContentLogin extends HtmlContent
implements HtmlContentInterface
{
    /**
     * Builds the Login Form to display to the Curator
     */
    public function setSource()
    {
        //Set Page Title/Header
        HeaderFooter::$title = 'Login';
        HeaderFooter::$headerDisplay = 'Login';
        
        //RETRIEVE FORM LAYOUT FROM XML FILE
        $formArray = PageConfiguration::getCurrentPageForms();
        //BEGIN MEME TABLE CONSTRUCTION
        $FormFieldSet = new HtmlFormFieldSet("Login","Login");
        $emptyData = new SimpleXMLElement("<base></base>");
        $FormFieldSet->setFormConfiguration($formArray->LoginBlock);
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
            .'<a href="'
            .ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
            .'ResetPassword/'
            .'">Forgot Password?</a>'
            .'</div>';
    }
}
