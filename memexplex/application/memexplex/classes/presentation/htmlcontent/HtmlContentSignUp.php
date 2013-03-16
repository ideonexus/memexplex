<?php

/**
 * Builds the XHTML content for displaying the signup form.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Kevin Flynn
 * @see HtmlContent
 * @see HtmlContentInterface
 */
class HtmlContentSignUp extends HtmlContent
implements HtmlContentInterface
{

    /**
     * Sets the HTML source.
     */
    public function setSource()
    {
        //Set Page Title/Header
        HeaderFooter::$title = 'Sign Up';
        HeaderFooter::$headerDisplay = 'Sign Up';
        
        //RETRIEVE FORM LAYOUT FROM XML FILE
        $formArray = PageConfiguration::getCurrentPageForms();
        //BEGIN MEME TABLE CONSTRUCTION
        $FormFieldSet = new HtmlFormFieldSet("SignUp","SignUp");
        $emptyData = new SimpleXMLElement("<base></base>");
        $FormFieldSet->setFormConfiguration($formArray->SignUpBlock);
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
            '<div align="center"><table class="layout"><tr><td>'
            .$Form->getSource()
            .'</td></tr></table></div>';
    }

}
