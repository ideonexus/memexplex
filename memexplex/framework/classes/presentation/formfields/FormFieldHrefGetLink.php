<?php

/**
 * Builds an Href GET link.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormField
 * @see FormFieldInterface
 */
class FormFieldHrefGetLink extends FormField
implements FormFieldInterface
{
    /**
     * @var string Destination URL.
     */
    protected $destination = '';

    /**
     * @var string Array of GET variables key/value.
     */
    protected $getArray = array();

    /**
     * Adds a get variable to the array.
     */
    public function addGetVariable($key,$value)
    {
        $this->getArray[$key] = $value;
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
            $this->addGetVariable
            (
                $key
                ,SimpleXml::getSimpleXmlItem
                (
                    $formData
                    ,$valueXpath
                )
            );
        }
        foreach($formfield->variables->children() as $key=>$value)
        {
            $this->addGetVariable
            (
                $key
                ,$value
            );
        }
    }

    /**
     * @param string $defaultValue
     */
    public function setDefaultValue($defaultValue = '')
    {
        $getString = '';
        $getId = '';
        if (count($this->getArray))
        {
            $divider = "";
            foreach ($this->getArray as $key => $value)
            {
                if ($key == "id")
                {
                    $getId = $value."/";
                }
                else
                {
                    $getString .= $divider . $key . "=" . $value;
                    $divider = "&";
                }
            }
        }

        $this->defaultValue = "<a href=\""
            . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
            . $this->destination
            . $getId
            . str_replace("/","",$getString)
            . "\">"
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
