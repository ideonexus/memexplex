<?php
/**
 * <todo:description>
 *
 * @package Framework
 * @subpackage Presentation
 * @author Henry Dorsett Case
 */
class FormFieldAuthorList extends FormField
implements FormFieldInterface
{
    /**
     * @var string <todo:description>
     */
    protected $formData;

    /**
     * @var string <todo:description>
     */
    protected $authorHiddenFields;

    /**
     * @var string <todo:description>
     */
    protected $authorString;

    public function setData
    (
        SimpleXMLElement $formfield
        ,SimpleXMLElement $formData
        ,SimpleXMLElement $pageObjectsXml
    )
    {
        $this->formData = $formData->xpath($formfield->valueXpath);
    }

    /**
     * <todo:description>
     *
     */
    public function setDefaultValue()
    {
        $separator = "";
        $increment = 0;
        $this->authorString = "";
        $this->authorHiddenFields = "";
        foreach ($this->formData as $author)
        {
            $this->authorString .= $separator . $author;
            $this->authorHiddenFields .= 
                '<input type="hidden"'
                .' id="'.$this->id.'_'.$increment.'"'
                .' value="'.$author.'">';
            $separator = ", ";
            $increment++;
        }
        
        if ($this->authorString == "")
        {
            $this->defaultValue = "None";
        }
        else
        {
            $this->defaultValue = 
                $this->authorString
                .$this->authorHiddenFields
                .'<input type="hidden"'
                .' id="'.$this->id.'rows"'
                .' value="'.$increment.'">';
        }
    }

    /**
     * <todo:description>
     *
     */
    public function setSource()
    {

        $this->source .=  "<input"
                         . " type=\"text\""
                         . " name=\"{$this->id}\""
                         . " id=\"{$this->id}\""
                         . " value=\"{$this->authorString}\""
                         . " size=\"{$this->size}\""
                         . " title=\"Enter Taxonomies separated by commas.\""
                         . " maxlength=\"{$this->maxlength}\""
                         . " disabled=\"disabled\""
                         . " />";

    }

}
