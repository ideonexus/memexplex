<?php

/**
 * Builds a form based on the page configuration.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 */

class HtmlForm
{

    /**
     * String containing the HTML for the form action.
     *
     * @var string
     */
    protected $formAction = "";

    /**
     * An array of values for hidden and other form fields
     * where the key is the form field id.
     *
     * @var array
     */
    protected $formVariables = array();

    /**
     * A string with the HTML content that will appear in the
     * main body of the form, between the FORM tags.
     *
     * @var string 
     */
    protected $formContent;

    /**
     * The SimpleXML configuration file describing the form name,
     * hidden variables, and buttons to appear at the top and bottom
     * of the form.
     *
     * @var SimpleXML object
     */
    protected $formConfiguration;

    /**
     * String containing the HTML to express the top of the form.
     *
     * @var string
     */
    protected $formTop = "";

    /**
     * String containing the HTML to express the bottom of the form.
     *
     * @var string
     */
    protected $formBottom = "";

    /**
     * String containing the HTML to express the top table of the form.
     *
     * @var string
     */
    protected $formTableTop = "";

    /**
     * String containing the HTML to express the bottom table of the form.
     *
     * @var string
     */
    protected $formTableBottom = "";

    /**
     * SimpleXML object gathering all data needed for the
     * page display. Referenced when display values
     * or arrays to populate select boxes are needed.
     *
     * @var SimpleXML object
     */
    protected $pageObjectsXml;

    /**
     * All data for the page.
     *
     * @param SimpleXmlObject $pageObjectsXml
     */
    public function setPageObjectsXml($pageObjectsXml = null)
    {
        $this->pageObjectsXml = $pageObjectsXml;
    }

    /**
     * Sets the form action attribute
     *
     * @param SimpleXmlObject $formConfiguration
     */
    public function setFormAction($formAction)
    {
        $this->formAction = " action=\"" . $formAction . "\"";
    }

    /**
     * Sets the form configuration.
     *
     * @param SimpleXmlObject $formConfiguration
     */
    public function setFormConfiguration($formConfiguration)
    {
        $this->formConfiguration = $formConfiguration;
    }

    /**
     * Add a form variable to the formVariables array,
     * which will populate the hidden variable with an
     * id that matches the key in the form.
     *
     * @param string $key
     * @param string $value
     */
    public function addFormVariable($key = '', $value = '')
    {
        $this->formVariables[$key] = $value;
    }

    /**
     * Append content to the HTML that will appear between the
     * opening and closing of the form. Executing this function
     * will not replace the existing $formContent, but append
     * additional content to it.
     *
     * @param string $html
     */
    public function appendFormContent($html = '')
    {
        $this->formContent .= $html;
    }

    /**
     * This method takes the $formConfiguration XML, loops through it,
     * and builds the top and bottom portions of the form.
     *
     * @throws {@link PresentationExceptionConfigurationError}
     */
    public function buildForm()
    {

        if (count((array)$this->formConfiguration) == 0)
        {
            throw new PresentationExceptionConfigurationError('A form configuration is required for HtmlForm::buildForm.');
        }

        //LOOP THOUGH FORM ELEMENTS
        $firstLoopThroughFormFields = true;
        foreach($this->formConfiguration->form as $form)
        {
            if (!$firstLoopThroughFormFields)
            {
                if ($this->formTableTop != "")
                {
                    $this->formTableTop .=
                         "</tr>"
                    ."</table>";
                }
                if ($this->formTableBottom != "")
                {
                    $this->formTableBottom .=
                         "</tr>"
                    ."</table>";
                }
                $this->formTop .= $this->formTableTop
                                  . $this->formBottom
                                  . $this->formTableBottom
                                  . "</form>";
                $this->formTableTop = "";
                $this->formBottom = "";
                $this->formTableBottom = "";
            }
            $formFocus = "";
            if (isset($form->focus))
            {
                $formFocus = "setTimeout('if ($(\'{$form->focus}\') != null){"
                    ."$(\'{$form->focus}\').focus();}',200);";
            }
            $this->formTop .=
         "<form id=\"{$form->id}\" name=\"{$form->id}\" method=\"post\"  onSubmit=\"return false\" {$this->formAction}>"
         . "<script type=\"text/javascript\">"
         . "addLoadEvent(function()"
         . "{"
         . $formFocus
         . "setTimeout('enableFormElements(\'{$form->id}\')',100);"
         . "});"
         . "</script>";

            foreach($form->formfield as $formfield)
            {
                $htmlFormField = FormFieldFactory::create($formfield->type);
                $htmlFormField->setId($formfield->id);
                if (isset($this->formVariables[(string) $formfield->id]))
                {
                    $htmlFormField->setDefaultValue($this->formVariables[(string) $formfield->id]);
                }
                elseif (isset($this->pageObjectsXml)
                    && SimpleXml::getSimpleXmlItem($this->pageObjectsXml,$formfield->valueXpath))
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

                //LOOP THROUGH CUSTOM METHODS
                if (isset($formfield->methods))
                {
                    foreach ($formfield->methods->children() as $method=>$input)
                    {
                        $htmlFormField->$method($input);
                    }
                }
                $htmlFormField->setSource();

                //DON'T WRITE TABLE CELLS FOR HIDDEN FORMELEMENTS
                if ((string) $formfield->type != 'Hidden')
                {
                    if ((string) $formfield->type == 'ButtonMore')
                    {
                        if ($this->formTableBottom == "")
                        {
                            $this->formTableBottom =
                    "<table class=\"layout\" style=\"width:100%;\">"
                    .    "<tr>"
                    .        "<td width=\"100%\">&nbsp</td>";
                        }

                        $this->formTableBottom .= "<td"
                        . ">&nbsp;"
                        . $htmlFormField->getSource()
                        . "&nbsp;</td>"
                        . "</tr></table>"
                        . "<table class=\"layout\" style=\"width:100%;\">"
                        .     "<tr>"
                        .         "<td width=\"100%\">&nbsp</td>";
                    }
                    else
                    {
                        if ($this->formTableBottom == "")
                        {
                            $this->formTableBottom =
                    "<table class=\"layout\" style=\"width:100%;\">"
                    .    "<tr>"
                    .        "<td width=\"100%\">&nbsp</td>";
                        }

                        $this->formTableBottom .= "<td"
                                 . ">&nbsp;"
                                 . $htmlFormField->getSource()
                                 . "&nbsp;</td>";
                    }
                }
                else
                {
                    $this->formTop .= $htmlFormField->getSource();
                }

            }//FORM ELEMENT LOOP

            $firstLoopThroughFormFields = false;

        }//FORM LOOP

        if ($this->formTableTop != "")
        {
            $this->formTableTop .=
                 "<td width=\"100%\"></td></tr>"
            ."</table>";
        }

        $this->formTop .= $this->formTableTop;
        $this->formTableTop = "";

        if ($this->formTableBottom != "")
        {
            $this->formTableBottom .=
                 "</tr>"
            ."</table>";
        }

        $this->formBottom .= $this->formTableBottom . "</form>";
        $this->formTableBottom = "";

    }

    /**
     * Returns the complete HTML for the form, including opening,
     * main, and closing content.
     *
     * @return string
     */
    public function getSource()
    {
        //ADD JAVASCRIPT AJAX PROCESS FORM TO INCLUDES
        JavaScript::addJavaScriptInclude("ajaxProcessForm");
        return "<div>"
               . $this->formTop
               . $this->formContent
               . $this->formBottom
               . "</div><br/>";
    }

    /**
     * Get just the opening portion of the form as HTML.
     *
     * @return string
     */
    public function getFormTopSource()
    {
        //ADD JAVASCRIPT AJAX PROCESS FORM TO INCLUDES
        JavaScript::addJavaScriptInclude("ajaxProcessForm");
        return $this->formTop;
    }

    /**
     * Get just the closing portion of the form as HTML.
     *
     * @return string
     */
    public function getFormBottomSource()
    {
        return $this->formBottom;
    }

}
