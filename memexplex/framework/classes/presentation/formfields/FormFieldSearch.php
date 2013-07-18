<?php

/**
 * Builds a search input with AJAX submit functionality.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormField
 * @see FormFieldInterface
 */
class FormFieldSearch extends FormField
implements FormFieldInterface
{

    /**
     * @var string Input title attribute.
     */
    protected $title;

    /**
     * @param string
     */
    public function setTitle($title)
    {
        $this->title = " title=\"{$title}\"";
    }

    /**
     * FormField SIZE attribute.
     * @var string
     */
    protected $size = "32";

    /**
     * FormField MAXLENGTH attribute.
     * @var string
     */
    protected $maxlength = "32";

    /**
     * @var string $size
     */
    public function setSize($size = "32")
    {
        $this->size = $size;
    }

    /**
     * @var string $maxlength
     */
    public function setMaxlength($maxlength = "32")
    {
        $this->maxlength = $maxlength;
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
        $value = "";
        if ($this->getViceAjax && isset($_GET[trim($this->id)]))
        {
            $value = $_GET[trim($this->id)];
        }
        
        $this->source =
            "<input"
            ." type=\"text\""
            . " name=\"{$this->id}\""
            . " id=\"{$this->id}\""
            . " value=\"{$value}\""
            . " size=\"{$this->size}\""
            . " title=\"Enter Search Text\""
            . " placeholder=\"Search\""
            . " maxlength=\"{$this->maxlength}\""
            . " disabled=\"disabled\""
            . " onkeydown=\"enterPressed(event);\""
            . " />"
            ."<input"
            ." type=\"button\""
            ." name=\"{$this->id}button\""
            ." id=\"{$this->id}button\""
            .$this->title
            ." value=\"Search\""
            . " onclick=\""
            . $onClickJavaScript;
            
            if ($this->getViceAjax)
            {
                $this->source .=
                    "assembleQueryStringAndSubmit('{$this->id}');"
                    ."\" disabled=\"disabled\" />"
                    ."<script type=\"text/javascript\">"
                    ."function enterPressed(evn) {"
                    ."if (window.event && window.event.keyCode == 13) {"
                      ."assembleQueryStringAndSubmit('{$this->id}');"
                    ."} else if (evn && evn.keyCode == 13) {"
                      ."assembleQueryStringAndSubmit('{$this->id}');"
                    ."}}"
                    ."</script>";
            }
            else
            {
                $this->source .=
                    "ajaxGetHTML();"
                    ."\" disabled=\"disabled\" />"
                    ."<script type=\"text/javascript\">"
                    ."function enterPressed(evn) {"
                    ."if (window.event && window.event.keyCode == 13) {"
                      ."ajaxGetHTML();"
                    ."} else if (evn && evn.keyCode == 13) {"
                      ."ajaxGetHTML();"
                    ."}}"
                    ."</script>";
            }
            
    }

}
