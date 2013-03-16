<?php

/**
 * This class builds an HTML FieldSet filled with report data
 * and form elements based on a SimpleXML object for its
 * configuration.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 */
class HtmlFormFieldSet
{

    /**
     * The SimpleXML configuration file describing the form name,
     * hidden variables, and buttons to appear at the top and bottom
     * of the form.
     *
     * @var SimpleXmlObject
     */
    protected $formConfiguration;

    /**
     * SimpleXML object containing the data specific to the
     * row being built (referenced by Xpath in the configuration),
     * or the complete collection of form data for all rows, which
     * the appendFormTable method will loop through and construct
     * the table automatically.
     *
     * @var SimpleXmlObject
     */
    protected $formData = null;

    /**
     * The ID of the table, as JavaScript and
     * process form would interact with it.
     *
     * @var string
     */
    protected $fieldSetId;

    /**
     * The name of the form, as the User would
     * best understand it.
     *
     * @var string
     */
    protected $fieldSetTitle;

    /**
     * Contains the HTML for the editable version of the form.
     *
     * @var string, HTML
     */
    protected $fieldSetSource = '';

    /**
     * An array of values for hidden and other form fields
     * where the key is the form field id.
     *
     * @var array
     */
    protected $formVariables = null;

    /**
     * SimpleXML object gathering all data needed for the
     * page display. Referenced when display values
     * or arrays to populate select boxes are needed.
     *
     * @var SimpleXmlObject
     */
    protected $modifier = "";

    /**
     * SimpleXML object gathering all data needed for the
     * page display. Referenced when display values
     * or arrays to populate select boxes are needed.
     *
     * @var SimpleXmlObject
     */
    protected $pageObjectsXml = null;

    /**
     * This string holds any additional information to be
     * appended to the section-break line just before a table,
     * such as last change date or href to an anchor tag.
     *
     * @var string
     */
    protected $sectionBreakAppend = '';

    /**
     * Allows setting the fieldSetTitle and fieldSetId on instantiation.
     */
    public function __construct($fieldSetTitle='',$fieldSetId='')
    {
        $this->fieldSetTitle = $fieldSetTitle;
        $this->fieldSetId = $fieldSetId;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function addFormVariable($key = '', $value = '')
    {
        $this->formVariables[$key] = $value;
    }

    /**
     * Sets all properties and methods for the FormField.
     * 
     * [TODO: Move this to the FormField Factory.]
     *
     * @param FormField $formfield
     */
    protected function setFormfield($formfield)
    {
        $htmlFormField = FormFieldFactory::create($formfield->type);
        $htmlFormField->setLabel($formfield->label);
        $htmlFormField->setId($formfield->id.$this->modifier);
        $htmlFormField->setData($formfield, $this->formData, $this->pageObjectsXml);

        //LOOP THROUGH CUSTOM METHODS
        if (isset($formfield->methods))
        {
            foreach ($formfield->methods->children() as $method=>$input)
            {
                $htmlFormField->$method($input);
            }
        }

        //IF A DISPLAY ONLY VALUE IS A COMBINATION OF VALUES
        if (isset($formfield->valueXpath->value))
        {
            $formFieldDisplayValue = "";
            //LOOP THROUGH THE XPATHS AND APPEND THE VALUES
            foreach ($formfield->valueXpath->value as $value)
            {
                //DON'T APPEND IF NOTHING TO APPEND
                if
                (
                    "" != SimpleXml::getSimpleXmlItem
                          (
                              $this->formData
                              ,$value
                          )
                )
                {
                     $formFieldDisplayValue .= $value["delimeter"]
                                         . SimpleXml::getSimpleXmlItem
                                         (
                                             $this->formData
                                             ,$value
                                         );
                }
            }
            $htmlFormField->setDefaultValue($formFieldDisplayValue);
        }
        elseif (SimpleXml::getSimpleXmlItem($this->formData,$formfield->valueXpath))
        {
            $htmlFormField->setDefaultValue
            (
                (string) SimpleXml::getSimpleXmlItem
                (
                    $this->formData
                    ,$formfield->valueXpath
                )
            );
        }
        //FORM VARIABLES MANUALLY SET IN THE PAGE, NOT IN XML
        else if (isset($this->formVariables[(string) $formfield->id]))
        {
           $htmlFormField->setDefaultValue
            (
                $this->formVariables[(string) $formfield->id]
            );
        }

        $htmlFormField->setSource();
        $htmlFormField->setJavaScriptValidation();

        return $htmlFormField;
    }

    /**
     * Builds an HTML FieldSet looping through the dataArray
     * provided to it as rows.
     */
    public function appendFormFieldSet()
    {
        // ADD EMPTY ELEMENT IF NO FORM DATA
        if (!$this->formData)
        {
            $this->formData = new SimpleXMLElement('<none></none>');
        }

        // ADD EMPTY ELEMENT IF NO PAGEOBJECTS DATA
        if (!$this->pageObjectsXml)
        {
            $this->pageObjectsXml = new SimpleXMLElement('<none></none>');
        }

        //LOOP THOUGH FORM ELEMENTS
        foreach($this->formConfiguration->formfield as $formfield)
        {
            $display = true;
            if ($formfield->display)
            {
                if (!$this->formData->xpath($formfield->display->ifXpath))
                {
                    $display = false;
                }
            }
            
            if ($display)
            {
                $htmlFormField = $this->setFormfield($formfield);
                if ((string) $formfield->type != 'Hidden')
                {
                    $labelInline = "<br/>";
                    $break = "<br/>";
                    $spanOpen = "";
                    $spanClose = "";
                    if (isset($formfield->nobreak))
                    {
                        $break = "&nbsp;";
                    }
                    if (isset($formfield->labelInline))
                    {
                        $labelInline = "&nbsp;";
                    }
                    if (isset($formfield->span))
                    {
                        $spanOpen = "<span ".$formfield->span->custom.">";
                        $spanClose = "</span>";
                    }

                    $this->fieldSetSource .=
                        $spanOpen
                    	. "<label for=\"{$formfield->id}\">"
                        . $formfield->label
                        . "</label>"
                        . $labelInline
                        . $htmlFormField->getSource()
                        . $break
                        . $spanClose;
                }
                else
                {
                    $this->fieldSetSource .= $htmlFormField->getSource();
                }
            }
        }
    }

    /**
     * Gets the FieldSet source.
     *
     * @param boolean $includeDiv Whether to include enclosing DIVS
     * @return string
     */
    public function getFieldSetSource($includeDiv=true)
    {
        if ($includeDiv)
        {
            return
                "<fieldset>"
                .    "<legend>{$this->fieldSetTitle}</legend>"
                .    $this->fieldSetSource
                ."</fieldset>";
        }
        else
        {
            return $this->fieldSetSource;
        }
    }

    /**
     * hr tag and title for section
     *
     * @return string
     */
    public function getSectionBreak()
    {
        return
            "<table class=\"layout\">"
            .  "<tr>"
            .    "<td><b>{$this->fieldSetTitle}:</b></td>"
            .    "<td width=\"100%\"><hr /></td>"
            .    $this->sectionBreakAppend
            .  "</tr>"
            ."</table>";
    }

    /**
     * Gets the fieldset id.
     *
     * @return string
     */
    public function getFieldSetId()
    {
        return $this->fieldSetId . "FieldSetEdit";
    }

    /**
     * @param SimpleXmlObject $formConfiguration
     */
    public function setFormConfiguration($formConfiguration)
    {
        $this->formConfiguration = $formConfiguration;
    }

    /**
     * @param SimpleXmlObject $formData
     */
    public function setFormData(SimpleXMLElement $formData)
    {
        $this->formData = $formData;
    }

    /**
     * @param string $fieldSetId
     */
    public function setFieldSetId($fieldSetId = '')
    {
        $this->fieldSetId = $fieldSetId;
    }

    /**
     * @param string $fieldSetTitle
     */
    public function setFieldSetTitle($fieldSetTitle = '')
    {
        $this->fieldSetTitle = $fieldSetTitle;
    }

    /**
     * Modifier in case you want to display the same fieldset twice on a page
     * but with different ids to identify the fields.
     * @param string $modifier
     */
    public function setModifier($modifier = null)
    {
        $this->modifier = $modifier;
    }

    /**
     * @param SimpleXmlObject $pageObjectsXml
     */
    public function setPageObjectsXml($pageObjectsXml = null)
    {
        $this->pageObjectsXml = $pageObjectsXml;
    }

    /**
     * @param string $text
     */
    public function setSectionBreakAppend($text = '')
    {
        $this->sectionBreakAppend .= "<td><b>$text</b></td>";
    }
}
