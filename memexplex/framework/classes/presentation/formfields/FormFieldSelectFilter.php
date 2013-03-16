<?php
/**
 * Builds HTML code for a selector for presentation to the client. This field
 * has an onchange trigger that submits and AJAX request.
 *
 * Note: $selectArray should be in 'value' => option format
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma 08/21/2008
 * @see FormFieldSelect
 * @see FormFieldInterface
 */
class FormFieldSelectFilter extends FormFieldSelect
implements FormFieldInterface
{
    /**
     * Acts as a label for the selector.
     * @var string
     */
    protected $firstOptionText = null;

    /**
     * Label for the selector.
     * @param $optionText
     */
    public function setFirstOptionText($optionText=null)
    {
        $this->firstOptionText = $optionText;
    }

    /**
     * Uses GET method instead of AJAX Call
     * @var boolean
     */
    protected $getViceAjax = false;

    /**
     * @var string $size
     */
    public function setGetViceAjax($getViceAjax = false)
    {
        $this->getViceAjax = $getViceAjax;
    }

    /**
     * Sets the source propery to the HTML string.
     */
    public function setSource()
    {

        $this->source = "<span id=\"alert{$this->id}\"></span>"
            . "<select name=\"{$this->id}\""
            . " id=\"{$this->id}\""
            . "{$this->onChangeJavaScript}"
            . " onchange=\"" . $onChangeJavaScript;
        
        if ($this->getViceAjax)
        {
            $this->source .= 
                "assembleQueryStringAndSubmit('{$this->id}',$('"
                . $this->id
                . "').options[$('"
                . $this->id
                . "').selectedIndex].value);";
        }
        else
        {
            $this->source .= 
                 "getContent('" . ROOT_FOLDER . "framework/api/setSessionVariables.php"
                . "','"
                . "application="
                . Constants::getConstant('CURRENT_APPLICATION')
                . "&pageCode="
                . Constants::getConstant('CURRENT_PAGE_CODE')
                . "&"
                . $this->id
                . "=' + $('"
                . $this->id
                . "').options[$('"
                . $this->id
                . "').selectedIndex].value,'');"
                ."ajaxGetHTML();";
        }
        
        $this->source .= 
             "\" disabled=\"disabled\""
            . ">";

        $displayOption = true;
        if ($this->noEmptyOption)
        {
            $displayOption = false;
        }
        $firstLoop = true;
        foreach ($this->selectArray as $key => $value)
        {
            if ($displayOption)
            {
                //SET THE SELECTOR TITLE AS FIRST OPTION
                if ($this->firstOptionText && $firstLoop)
                {
                    $key = "";
                    $value = $this->firstOptionText;
                }
                $firstLoop = false;

                $optionId = "";

                $this->selectedDisplay = "";
                if ($this->defaultValue == $key)
                {
                    $this->selectedDisplay = " selected=\"selected\"";
                }
                elseif ($this->getViceAjax 
                    && isset($_GET[trim($this->id)])
                    && $_GET[trim($this->id)] == $key
                )
                {
                    $this->selectedDisplay = " selected=\"selected\"";
                }

                if (null != $this->parentArray)
                {
                    foreach ($this->parentArray[$key] as $class)
                    {
                        if (null != $this->idArray)
                        {
                            $optionId = " id=\"" . $this->idArray[$key][$class] . "\"";
                        }
                        else
                        {
                            $optionId = "";
                        }
                        $this->source .= "<option value=\"{$key}\""
                        . " class=\"{$class}\""
                        . $optionId
                        . $this->selectedDisplay
                        . ">{$value}</option>";
                    }
                }
                else
                {
                    $this->source .= "<option value=\"{$key}\""
                    . $optionId
                    . $this->selectedDisplay
                    . ">{$value}</option>";
                }
            }
            $displayOption = true;
        }

        $this->source .= "</select>\n";

    }

    /**
     * Gets the source.
     */
    public function getSource($view=false)
    {
        if ($view)
        {
            if ($this->displayValue == "")
            {
                return "&nbsp;";
            }
            else
            {
                return $this->displayValue;
            }
        }
        else
        {
            if ($this->source == "")
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
