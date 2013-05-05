<?php

/**
 * More button for adding rows to a table or form.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormField
 * @see FormFieldInterface
 */
class FormFieldButtonMore extends FormField
implements FormFieldInterface
{

    /**
     * @var string Image ALT attribute.
     */
    protected $altTag;

    /**
     * @var string Link HREF attribute.
     */
    protected $hrefTag = '#';

    /**
     * @var string Image SRC attribute.
     */
    protected $imageSource = '';

    /**
     * @var string More function javascript.
     */
    protected $javaScript = '';

    /**
     * @var string Number of table rows to clone.
     */
    protected $numberOfRowsToClone = 1;

    /**
     * @var string Hidden form element to keep count of rows.
     */
    protected $rowCountField = '';

    /**
     * @var string Table ID to add rows to.
     */
    protected $tableId = '';

    /**
     * @param string
     */
    public function setAltTag($altTag = '')
    {
        $this->altTag = " alt=\"{$altTag}\"";
    }

    /**
     * @param string
     */
    public function setDefaultMoreJavaScript()
    {
        $this->hrefTag = "javascript:addTableRow('{$this->tableId}',$this->numberOfRowsToClone,parseInt($('{$this->rowCountField}').value));";
        $this->javaScript =
            "<script type=\"text/javascript\">"
            //INCREMENT COUNTER FOR MAIN ROW
            ."function increment{$this->rowCountField}()"
            ."{"
                ."$('{$this->rowCountField}').value++;"
            ."}"
            ."addLoadEvent(function()"
            ."{"
                ."setTimeout('cloneTableRow(\'{$this->tableId}\',{$this->numberOfRowsToClone},parseInt($(\'{$this->rowCountField}\').value))',500);";
            if (!Constants::getConstant('AJAX_METHOD'))
            {
                $this->javaScript .=
                "setTimeout('addTableRowObserver.subscribe(increment{$this->rowCountField})',500);";
            }
        $this->javaScript .=
             "});"
            ."</script>";
        //INCLUDE STANDARD JAVASCRIPT MORE FUNCTION
        JavaScript::addJavaScriptInclude("addTableRow");
    }

    /**
     * @param string
     */
    public function setHrefTag($hrefTag = '')
    {
        $this->hrefTag = $hrefTag;
    }

    /**
     * @param string
     */
    public function setImageSource($image = '')
    {
        $this->imageSource = " src=\"" . ROOT_FOLDER . "framework/images/{$image}\"";
    }

    /**
     * @var string
     */
    public function setNumberOfRowsToClone($numberOfRowsToClone = 1)
    {
        $this->numberOfRowsToClone = $numberOfRowsToClone;
    }

    /**
     * @var string
     */
    public function setRowCountField($rowCountField = '')
    {
        $this->rowCountField = $rowCountField;
    }

    /**
     * Builds the more button and associated javascript.
     */
    public function setSource()
    {

        if ($this->imageSource == "")
        {
            $this->imageSource = " src=\"" . ROOT_FOLDER . "framework/images/add-button.png\"";
        }

        $this->source .=
            "<div class=\"moreButtonDiv\" id=\"{$this->id}\">"
                . "<a href=\""
                . $this->hrefTag
                . "\">"
                    . "<img"
                    . $this->imageSource
                    . $this->altTag
                    . " class=\"moreButton\""
                    . " />"
                . "</a>"
            . "</div>"
            . $this->javaScript;

    }

    /**
     * @var string <todo:description>
     */
    public function setTableId($tableId = '')
    {
        $this->tableId = $tableId;
    }

}
