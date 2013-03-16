<?php

/**
 * Builds a delete button.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormField
 * @see FormFieldInterface
 */
class FormFieldButtonDelete extends FormField
implements FormFieldInterface
{
    /**
     * @var string
     */
    protected $defaultValue = 'Delete';

    /**
     * @var string
     */
    protected $title = 'Click to Delete.';

    /**
     * @var string Form ID
     */
    protected $form;

    /**
     * @var string
     */
    public function setForm($form="")
    {
        $this->form = $form;
    }

    /**
     * @var string "Are you sure you want to delete the *recordtype*?"
     */
    public function setConfirmation($recordType)
    {
        $this->setOnClickJavaScript(
        	'return confirmDelete(\'' . $recordType . '\',\'' . $this->form . '\');'
        );
        JavaScript::addJavaScriptInclude("subModal");
        CascadingStyleSheets::addCascadingStyleSheetsInclude("subModal");
    }

    /**
     * Sets the source propery to the HTML string.
     */
    public function setSource()
    {
        $this->source = "<input"
                        ." type=\"button\""
                        ." name=\"{$this->id}\""
                        ." id=\"{$this->id}\""
                        .$this->title
                        ." value=\"{$this->defaultValue}\""
                        .$this->onClickJavaScript
                        ." disabled=\"disabled\" />"
                        ."<input"
                        ." type=\"hidden\""
                        ." name=\"deletefunction\""
                        ." id=\"deletefunction\""
                        ." value=\"\" />";
    }

}
