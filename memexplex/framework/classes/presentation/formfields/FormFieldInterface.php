<?php

/**
 * These are the properties and methods all FormFields must have.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 */
interface FormFieldInterface
{

    /**
     *  The formelement ID and NAME attribute.
     */
    function setId();

    /**
     * Formfield Label display.
     */
    function setLabel();

    /**
     * Additional data for setting custom properties that are not handled
     * automatically through other methods.
     *
     * @param SimpleXMLElement $formfield
     * @param SimpleXMLElement $formData
     * @param SimpleXMLElement $pageObjectsXml
     */
    public function setData
    (
        SimpleXMLElement $formfield
        ,SimpleXMLElement $formData
        ,SimpleXMLElement $pageObjectsXml
    );

    /**
     * Value displayed in view-only mode and in textbox.
     */
    function setDefaultValue();

    /**
     * Is the field clonable?
     */
    function setClonable();

    /**
     * JavaScript executed onchange.
     */
    function setOnChangeJavaScript();

    /**
     * JavaScript executed onclick.
     */
    function setOnClickJavaScript();

    /**
     * Build the HTML for the form.
     */
    function setSource();

    /**
     * Reference to JavaScript to validate field on save.
     */
    function setJavaScriptValidation();

    /**
     * Gets the HTML source.
     */
    function getSource();

}
