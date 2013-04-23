<?php

/**
 * This class builds an HTML chunk filled with report data
 * and form elements based on a SimpleXML object for its
 * configuration.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 */
class HtmlFormBlock
{

    /**
     * The SimpleXML configuration file describing the form name,
     * hidden variables, and buttons to appear at the top and bottom
     * of the form.
     *
     * @var SimpleXML Object
     */
    protected $formConfiguration;

    /**
     * SimpleXML object containing the data specific to the
     * row being built (referenced by Xpath in the configuration),
     * or the complete collection of form data for all rows, which
     * the appendFormTable method will loop through and construct
     * the table automatically.
     *
     * @var SimpleXML object
     */
    protected $formData = array();

    /**
     * The ID of the table, as JavaScript and
     * process form would interact with it.
     *
     * @var string
     */
    protected $blockId;

    /**
     * The name of the form, as the User would
     * best understand it.
     *
     * @var string
     */
    protected $blockTitle;

    /**
     * Contains the HTML for the editable version of the form.
     *
     * @var string, HTML
     */
    protected $blockSource = '';

    /**
     * An array of values for hidden and other form fields
     * where the key is the form field id.
     *
     * @var array
     */
    protected $formVariables = array();

    /**
     * SimpleXML object gathering all data needed for the
     * page display. Referenced when display values
     * or arrays to populate select boxes are needed.
     *
     * @var SimpleXmlObject
     */
    protected $pageObjectsXml = array();

    /**
     * This string holds any additional information to be
     * appended to the section-break line just before a table,
     * such as last change date or href to an anchor tag.
     *
     * @var string
     */
    protected $sectionBreakAppend = '';

    /**
     * Allows setting the blockTitle and blockId on instantiation.
     */
    public function __construct($blockTitle='',$blockId='')
    {
        $this->blockTitle = $blockTitle;
        $this->blockId = $blockId;
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
     * Sets all properties and methods for a formfield
     *
     *[TODO: Move all this to the FormField factory.]
     *
     * @param FormField $formfield
     */
    protected function setFormfield($formfield)
    {
        $htmlFormField = FormFieldFactory::create($formfield->type);
        $htmlFormField->setLabel($formfield->label);
        $htmlFormField->setId($formfield->id);
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
        elseif ((string)SimpleXml::getSimpleXmlItem($this->pageObjectsXml,$formfield->valueXpath)
        	|| SimpleXml::getSimpleXmlItem($this->pageObjectsXml,$formfield->valueXpath))
        {
            $htmlFormField->setDefaultValue
            (
                (string) SimpleXml::getSimpleXmlItem
                (
                    $this->pageObjectsXml
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
     * Builds an HTML form block looping through the dataArray
     * provided to it as rows.
     */
    public function appendFormBlock()
    {
        // ADD EMPTY ELEMENT IF NO FORM DATA
        if (count((array)$this->formData) == 0)
        {
            $this->formData = new SimpleXMLElement('<none></none>');
        }

        //LOOP THOUGH FORM ELEMENTS
        foreach($this->formConfiguration->formfield as $formfield)
        {
            $htmlFormField = $this->setFormfield($formfield);
            $this->blockSource .= $htmlFormField->getSource();
        }
    }

    /**
     * Gets the form block source.
     *
     * @param boolean $includeDiv With or without DIV tags around it.
     * @return string
     */
    public function getBlockSource($includeDiv=true)
    {
        if ($includeDiv)
        {
            return
                "<table class=\"layout\" width=\"100%\">"
                .  "<tr>"
                .    "<td><b>{$this->blockTitle}:"
                .    "<a class=\"noLink\" name=\"{$this->blockId}AnchorEdit\"></a>"
                .    "</b></td>"
                .    "<td width=\"100%\"><hr /></td>"
                .    $this->sectionBreakAppendEdit
                .  "</tr>"
                ."</table>"
                ."<div"
                .    " id=\"{$this->blockId}BlockEdit\">"
                .    $this->blockSource
                ."</div>";
        }
        else
        {
            return $this->blockSource;
        }
    }

    /**
     * Get just the hr tag and title of the form.
     *
     * @return <type> <todo:description>
     */
    public function getSectionBreak()
    {
        return
            "<table class=\"layout\">"
            .  "<tr>"
            .    "<td><b>{$this->blockTitle}:</b></td>"
            .    "<td width=\"100%\"><hr /></td>"
            .    $this->sectionBreakAppend
            .  "</tr>"
            ."</table>";
    }

    /**
     * Get the block id... I think this isn't used.
     *
     * @return string
     */
    public function getBlockId()
    {
        return $this->blockId . "BlockEdit";
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
    public function setFormData($formData = array())
    {
        $this->formData = $formData;
    }

    /**
     * @param string $blockId
     */
    public function setBlockId($blockId = '')
    {
        $this->blockId = $blockId;
    }

    /**
     * @param string $blockTitle
     */
    public function setBlockTitle($blockTitle = '')
    {
        $this->blockTitle = $blockTitle;
    }

    /**
     * @param SimpleXmlObject $pageObjectsXml
     */
    public function setPageObjectsXml($pageObjectsXml = array())
    {
        $this->pageObjectsXml = $pageObjectsXml;
    }

    /**
     * @param string $blockTitle
     */
    public function setSectionBreakAppend($text = '')
    {
        $this->sectionBreakAppend .= "<td><b>$text</b></td>";
    }
}
