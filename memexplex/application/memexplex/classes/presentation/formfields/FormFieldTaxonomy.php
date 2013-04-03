<?php
/**
 * <todo:description>
 *
 * @package Framework
 * @subpackage Presentation
 * @author Henry Dorsett Case
 */
class FormFieldTaxonomy extends FormFieldText
implements FormFieldInterface
{
    /**
     * @var string <todo:description>
     */
    protected $formData;

    /**
     * @var string <todo:description>
     */
    protected $taxonomyLinks;

    /**
     * @var string <todo:description>
     */
    protected $taxonomyString;

    /**
     * @var string <todo:description>
     */
    protected $destination = '';

    /**
     * <todo:description>
     *
     */
    public function getTaxonomyString()
    {
        return $this->taxonomyString;
    }

    public function setData
    (
        SimpleXMLElement $formfield
        ,SimpleXMLElement $formData
        ,SimpleXMLElement $pageObjectsXml
    )
    {
        $this->formData = $formData;
    }

    /**
     * <todo:description>
     *
     */
    public function setDestination($destination='')
    {
        $this->destination = $destination;
    }

    /**
     * <todo:description>
     *
     */
    public function setDefaultValue()
    {
        $separator = "";
        $taxonomyCount = 0;
        $this->taxonomyString = "";
        $this->taxonomyLinks = '<span class="menulink">';
        foreach ($this->formData->TaxonomyList->Taxonomy as $taxonomy)
        {
        		$taxonomyCount++;
        		$lineBreak = "";
        		if ($taxonomyCount == 6)
        		{
        				$lineBreak = "<br/>";
        		}
            $this->taxonomyString .= $separator . $taxonomy;
            $this->taxonomyLinks .= '<a href="'
            . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
            . $this->destination
            . 'taxonomy=' . $taxonomy
            . '">'
            . $taxonomy
            . '</a>' . $lineBreak;
            $separator = ",";
        }
        if ($this->taxonomyLinks == "")
        {
            $this->defaultValue = "None";
        }
        else
        {
        	  $this->taxonomyLinks .= '</span>';
            $this->defaultValue = $this->taxonomyLinks;
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
                         . " value=\"{$this->taxonomyString}\""
                         . " size=\"{$this->size}\""
                         . " title=\"Enter Taxonomies separated by commas.\""
                         . " maxlength=\"{$this->maxlength}\""
                         . " disabled=\"disabled\""
                         . " />";

    }

}
