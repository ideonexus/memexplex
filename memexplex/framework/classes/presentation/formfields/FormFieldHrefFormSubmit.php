<?php

/**
 * Href tag with JavaScript to submit additional variables as POSTs.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormField
 * @see FormFieldInterface
 */
class FormFieldHrefFormSubmit extends FormField
implements FormFieldInterface
{
    /**
     * @var string Destination URL.
     */
    protected $destination = '';

    /**
     * @var string Array of POST variables key/value.
     */
    protected $postArray = array();

    /**
     * Adds a post variable to the array.
     */
    public function addPostVariable($key,$value)
    {
        $this->postArray[$key] = $value;
    }

    /**
     * Loops through <variablesXpaths> node elements and adds them to
     * the $postArray
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
    )
    {
        foreach($formfield->variablesXpaths->children() as $key=>$valueXpath)
        {
            $this->addPostVariable
            (
                $key
                ,SimpleXml::getSimpleXmlItem
                (
                    $formData
                    ,$valueXpath
                )
            );
        }
    }

    /**
     * @param string $defaultValue
     */
    public function setDefaultValue($defaultValue = '')
    {
        $postString = '';
        if (count($this->postArray))
        {
            foreach ($this->postArray as $key => $value)
            {
                $postString .= $key . "','" . $value;
            }
            $postString = "'" . $postString . "'";
        }

        $this->defaultValue = "<a href=\"javascript:MenuFormSubmit('"
            . ROOT_FOLDER
            . $this->destination
            . "',"
            . $postString
            . ");\">"
            . $defaultValue
            . "</a>";
    }

    /**
     * HREF Link.
     */
    public function setDestination($destination='')
    {
        $this->destination = $destination;
    }

    /**
     * Source equals default value, this is a view-only formelement.
     */
    public function setSource()
    {
        $this->source = $this->defaultValue;
    }

}
