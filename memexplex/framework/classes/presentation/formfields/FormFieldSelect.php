<?php

/**
 * Builds a select field.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma 08/21/2008
 * @see FormField
 * @see FormFieldInterface
 */
class FormFieldSelect extends FormField
implements FormFieldInterface
{

    /**
     * Value to display, the option display, not value.
     * @var string
     */
    protected $displayValue = '';

    /**
     * A child select ID, filtered on values of this formfield.
     * @var string
     */
    protected $childSelect = '';

    /**
     * 
     * @var array
     */
    protected $parentArray = null;

    /**
     * Array of ID attributes for each option.
     * @var array
     */
    protected $idArray = null;

    /**
     * Select Array.
     * @var array
     */
    protected $selectArray = null;

    /**
     * Set to true for no empty option in the selector.
     * @var boolean
     */
    protected $noEmptyOption = false;

    /**
     * @param $optionText
     */
    public function setNoEmptyOption()
    {
        $this->noEmptyOption = true;
    }

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
     * Builds the select arrays from SimpleXmlObjects.
     *
     * @param array $valuesArray
     * @param array $descriptionsArray
     * @param bool $classesArray
     * @return array
     */
    public static function buildSelectArraysFromXml
    (
        $valuesArray = false
        ,$descriptionsArray = false
        ,$classesArray = false
        ,$idsArray = false
    )
    {
        $arrayCount = 0;
        $selectArray = array();
        $classArray = array();
        $idArray = array();
        $maxOptionStringLength = 0;
        if (!$valuesArray->none)
        {
            foreach ($valuesArray as $item)
            {
                $selectArray[(string) $item] = (string) $descriptionsArray[$arrayCount];


                if ($maxOptionStringLength < strlen((string) $descriptionsArray[$arrayCount]))
                {
                    $maxOptionStringLength = strlen((string) $descriptionsArray[$arrayCount]);
                }

                if ($classesArray)
                {
                    $classArray[(string) $item][] = (string) $classesArray[$arrayCount];

                    //IDS ARE ONE FOR ONE IN RELATION TO CLASSES
                    if ($idsArray)
                    {
                        $idArray[(string) $item][(string) $classesArray[$arrayCount]] = (string) $idsArray[$arrayCount];
                    }
                }


                $arrayCount++;
            }

            //EMPTY OPTION
            if ($maxOptionStringLength > 0)
            {
                $emptyOption = '';
                if ($classesArray)
                {
                    //ADD ENOUGH SPACES TO ACT AS PLACEHOLDER FOR DYNAMIC SELECTS
                    for ($i = 0; $i < ($maxOptionStringLength * 1.5); $i++)
                    {
                        $emptyOption .= '&nbsp;';
                    }
                }

                //THIS METHOD PRESERVES ARRAY KEYS
                $tempArray = array(''=>$emptyOption);
                $selectArray = $tempArray + $selectArray;

                if ($idsArray)
                {
                    $tempArray = array(""=>array(""=>"%placeholder%"));
                    $idArray = $tempArray + $idArray;
                }

                if ($classesArray)
                {
                    $tempArray = array(""=>array(""=>"%placeholder%"));
                    $classArray = $tempArray + $classArray;
                }

                unset($tempArray);
            }
        }

        $returnArrays = array();
        $returnArrays['options'] = $selectArray;
        $returnArrays['classes'] = $classArray;
        $returnArrays['ids'] = $idArray;

        return $returnArrays;
    }

    /**
     * Sets the arrays to loop through to build the OPTIONS.
     * 
     *   <formfield>
     *       <label>Wing Type</label>
     *       <type>Select</type>
     *       <id>wingType</id>
     *       <attributeCode>arcrft_witp_qual</attributeCode>
     *       <valueXpath>
     *         PersonnelAttributeList
     *             /PersonnelAttribute
     *             [
     *                 Name='wing_type_qual'
     *             ]
     *                 /Value
     *       </valueXpath>
     *       <valueDisplayXpath>
     *         WingTypeList
     *             /option
     *             [
     *                 value='%VALUE%'
     *             ]
     *                 /description
     *       </valueDisplayXpath>
     *       <selectValuesXpath>
     *         WingTypeList
     *             /option
     *                 /value
     *       </selectValuesXpath>
     *       <selectDescriptionsXpath>
     *         WingTypeList
     *             /option
     *                 /description
     *       </selectDescriptionsXpath>
     *   </formfield>
     *
     * @param string SimplXml $formfield
     * @param string SimplXml $formData
     * @param string SimplXml $pageObjectsXml
     *
     * [TODO:The $selectArrays should be static, so that
     * they are not rebuilt everytime a row needs another
     * dropdown selector.]
     */
    public function setData
    (
        SimpleXMLElement $formfield
        ,SimpleXMLElement $formData
        ,SimpleXMLElement $pageObjectsXml
    )
    {
        $optionsArrayName = $this->id . "OptionsArray";
        $classesArrayName = $this->id . "ClassesArray";
        $idsArrayName     = $this->id . "IdsArray";

        // BUILD SELECT ARRAY
        $selectArrays = self::buildSelectArraysFromXml
        (
            $formfield->selectValuesXpath
                ? $pageObjectsXml->xpath
                  (
                      $formfield->selectValuesXpath
                  )
                : false
            ,$formfield->selectDescriptionsXpath
                ? $pageObjectsXml->xpath
                  (
                      $formfield->selectDescriptionsXpath
                  )
                : false
            ,$formfield->selectClassXpath
                ? $pageObjectsXml->xpath
                  (
                      $formfield->selectClassXpath
                  )
                : false
            ,$formfield->selectIdXpath
                ? $pageObjectsXml->xpath
                  (
                      $formfield->selectIdXpath
                  )
                : false
        );

        ${$optionsArrayName} = $selectArrays['options'];
        if (array_key_exists('classes',$selectArrays))
        {
            ${$classesArrayName} = $selectArrays['classes'];
        }
        if (array_key_exists('ids',$selectArrays))
        {
            ${$idsArrayName} = $selectArrays['ids'];
        }
        $this->setSelectArray(${$optionsArrayName});
        $this->setParentArray(${$classesArrayName});
        $this->setIdArray(${$idsArrayName});

        //DISPLAY OPTION VICE VALUE FOR SELECT FORMELEMENTS
        if (isset($formfield->valueDisplayXpath))
        {
            $this->setDisplayValue
            (
                SimpleXml::getSimpleXmlItem
                (
                    $pageObjectsXml
                    ,str_replace
                    (
                        "%VALUE%"
                        ,SimpleXml::getSimpleXmlItem
                        (
                            $formData
                            ,$formfield->valueXpath
                        )
                        ,$formfield->valueDisplayXpath
                    )
                )
            );
        }
    }

    /**
     * @param array $selectArray
     */
    public function setSelectArray($selectArray = null)
    {
        $this->selectArray = $selectArray;
    }

    /**
     * @param string $childSelect
     */
    public function setChildSelect($childSelect = '')
    {
        $this->childSelect = $childSelect;
    }

    /**
     * @param string $displayValue
     */
    public function setDisplayValue($displayValue = '')
    {
        $this->displayValue = $displayValue;
    }

    /**
     * @param array $idArray
     */
    public function setIdArray($idArray = null)
    {
        $this->idArray = $idArray;
    }

    /**
     * @param array $parentArray <todo:description>
     */
    public function setParentArray($parentArray = null)
    {
        $this->parentArray = $parentArray;
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
                        . " disabled=\"disabled\""
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
                if ($firstLoop)
                {
                    $key = "";
                    if ($this->firstOptionText != null )
                    {
                    		$value = $this->firstOptionText;
                    }
                    else
                    {
                    	$value = $this->label;
                    }
                    $disabled = " disabled  style=\"display:none;\"";
                }
                $firstLoop = false;

                $optionId = "";

                $this->selectedDisplay = "";
                if ($this->defaultValue == $key)
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
     * If Child select, set dynamic selector javascript for it.
     */
    public function setJavaScriptValidation()
    {
        if ($this->childSelect != '')
        {
            $idViceClass = "false";
            if (null != $this->idArray)
            {
                $idViceClass = "true";
            }

            $this->source .= "<script type=\"text/javascript\">"
                        . "var {$this->id}DynamicSelector;"
                        . "addLoadEvent(function()"
                        . "{"
                        .     "setTimeout('{$this->id}DynamicSelector = new DynamicSelector',100);"
                        .     "setTimeout('{$this->id}DynamicSelector.initialize(\"{$this->id}\",\"true\",\"$idViceClass\")',100);"
                        .     "setTimeout('{$this->id}DynamicSelector.addChild(\"{$this->childSelect}\")',100);"
                        .     "setTimeout('{$this->id}DynamicSelector.refreshOptions()',100);"
                        . "});"
                        . "</script>";
        }
    }

    /**
     * Sets a display value as view version and select as editable.
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
                if ($this->childSelect != '')
                {
                    //INCLUDE DYNAMIC SELECTOR JAVASCRIPT
                    JavaScript::addJavaScriptInclude("dynamicSelector");
                }
                return $this->source;
            }
        }
    }
}
