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
     * Uses GET method instead of AJAX Call
     * @var boolean
     */
    protected $getViceAjax = false;

    /**
     * @var string $size
     */
    public function setGetViceAjax($getViceAjax = false)
    {
				if ($getViceAjax == 'true')
				{
        	$this->getViceAjax = true;
        }
    }

    /**
     * Sets the source propery to the HTML string.
     */
    public function setSource()
    {

        $this->source = "<span id=\"alert{$this->id}\"></span>"
        		. "<div class=\"styled-select\" id=\"div{$this->id}\">"
            . "<select name=\"{$this->id}\""
            . " id=\"{$this->id}\""
            . " placeholder=\"{$this->label}\""
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
        	  $disabled = "";
            if ($displayOption)
            {
                //SET THE SELECTOR TITLE AS FIRST OPTION
                if ($this->firstOptionText != null && $firstLoop)
                {
                    $key = "";
                    $value = $this->firstOptionText;
                    $disabled = " disabled  style=\"display:none;\"";
                }
                $firstLoop = false;

                $optionId = "";

                $this->selectedDisplay = "";
                if ($this->defaultValue == $key)
                {
                    $this->selectedDisplay = " selected";
                }
                elseif ($this->getViceAjax 
                    && isset($_GET[trim($this->id)])
                    && $_GET[trim($this->id)] == $key
                )
                {
                    $this->selectedDisplay = " selected";
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
                        . $disabled
                        . $this->selectedDisplay
                        . ">{$value}</option>";
                    }
                }
                else
                {
                    $this->source .= "<option value=\"{$key}\""
                    . $optionId
                    . $disabled
                    . $this->selectedDisplay
                    . ">{$value}</option>";
                }
            }
            $displayOption = true;
        }

        $this->source .= "</select></div>\n";

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
