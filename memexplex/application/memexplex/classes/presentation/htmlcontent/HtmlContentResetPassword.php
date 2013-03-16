<?php

/**
 * Builds the main XHTML content for the Reset Password page.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Dr. Eric Vornoff
 * @see HtmlContent
 * @see HtmlContentInterface
 */
class HtmlContentResetPassword extends HtmlContent
implements HtmlContentInterface
{
    /**
     * Builds the Reset Password Form to display to the Curator
     */
    public function setSource()
    {
        //RETRIEVE FORM LAYOUT FROM XML FILE
        $formArray = PageConfiguration::getCurrentPageForms();
        //BEGIN MEME TABLE CONSTRUCTION
        $FormFieldSet = new HtmlFormFieldSet("Reset Password","ResetPassword");
        $emptyData = new SimpleXMLElement("<base></base>");
        $FormFieldSet->setFormConfiguration($formArray->ResetPasswordBlock);
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
