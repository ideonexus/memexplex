<?php
/**
 * Abstract class defining all properties and methods of all FormField objects.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see Html
 */
abstract class FormField extends Html
{

    /**
     * @var string The formelement ID and NAME attribute.
     */
    protected $id;

    /**
     * @var string Formfield Label display.
     */
    protected $label;

    /**
     * @var mixed What to display for view only version.
     */
    protected $defaultValue;

    /**
     * @var bool Is the formelement cloneable via javascript.
     */
    protected $clonable = false;

    /**
     * @var string The formelement ONCHANGE attribute.
     */
    protected $onChangeJavaScript = '';

    /**
     * @var string The formelement ONCLICK attribute.
     */
    protected $onClickJavaScript = '';

    /**
     * @param string $id
     */
    public function setId($id = '')
    {
        $this->id = $id;
    }

    /**
     * @param string $label
     */
    public function setLabel($label = '')
    {
        $this->label = $label;
    }

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
    )
    {
        //None default.
    }

    /**
     * @param string $defaultValue
     */
    public function setDefaultValue($defaultValue = '')
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * @param bool $clonable
     */
    public function setClonable($clonable = false)
    {
        $this->clonable = $clonable;
    }

    /**
     * @param string $onChangeJavaScript
     */
    public function setOnChangeJavaScript($onChangeJavaScript = '')
    {
        if ($onChangeJavaScript != '')
        {
            $this->onChangeJavaScript = " onchange=\"" . str_replace("%this%", "'" . $this->id . "'", $onChangeJavaScript) . "\"";
        }
    }

    /**
     * @param string $onChangeJavaScript
     */
    public function setOnClickJavaScript($onChangeJavaScript = '')
    {
        if ($onChangeJavaScript != '')
        {
            $this->onClickJavaScript = " onclick=\"" . str_replace("%this%", "'" . $this->id . "'", $onChangeJavaScript) . "\"";
        }
    }

    /**
     * Reference to JavaScript to validate field on save.
     */
    public function setJavaScriptValidation()
    {
        //EMPTY
    }

    /**
     * @param string $defaultValue
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Gets the HTML source.
     * 
     * @param boolean $view If true, show the default value. 
     */
    public function getSource($view=false)
    {
        if ($view)
        {
            if ($this->defaultValue == '')
            {
                return "&nbsp;";
            }
            else
            {
                return $this->defaultValue;
            }
        }
        else
        {
            if ($this->source == '')
            {
                return "&nbsp;";
            }
            else
            {
                return $this->source;
            }
        }
    }

}
