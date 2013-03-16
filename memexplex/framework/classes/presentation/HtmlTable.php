<?php

/**
 * This class builds an HTML table filled with report data
 * and form elements based on a SimpleXML object for its
 * configuration.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 */
class HtmlTable
{

    /**
     * String to append to the end of form field IDs, often
     * a number incremented with each loop distinguishing one
     * row from the next.
     *
     * @var string
     */
    protected $currentRow = '';

    /**
     * Editable number of columns in the current row.
     *
     * @var integer
     */
    protected $currentFormRowColumnSpan = 0;

    /**
     * As the methods loops through or are called individually
     * to build table rows, this variable holds the HTML for
     * the current row's editable version. This variable is
     * broken out to allow complete customization.
     *
     * @var string/HTML
     */
    protected $currentFormRowTableCells = '';

    /**
     *
     * @var string/HTML $currentRowTableCells
     * As the methods loop through or are
     * called individually to build table rows, this variable
     * holds the HTML for the current row's viewonly version.
     * This variable is broken out to allow complete customization.
     *
     */
    protected $currentRowTableCells = '';

    /**
     * Viewonly number of columns in the current row.
     *
     * @var integer
     */
    protected $currentRowColumnSpan = 0;

    /**
     * True/False indicator determining whether to build
     * the editable version of the table or just the
     * viewable version.
     *
     * @var Boolean
     */
    protected $editPrivileges = false;

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
    protected $tableData = array();

    /**
     * The ID of the table, as JavaScript and
     * process form would interact with it.
     *
     * @var string
     */
    protected $tableId;

    /**
     * The name of the form, as the User would
     * best understand it.
     *
     * @var string
     */
    protected $tableTitle;

    /**
     * Contains the HTML for the editable version of the form.
     *
     * @var string, HTML
     */
    protected $formTableSource = '';

    /**
     * Contains the HTML for the viewOnly version of the form.
     *
     * @var string, HTML
     */
    protected $tableSource = '';

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
     * @var SimpleXML object
     */
    protected $pageObjectsXml = array();

    /**
     * This array holds the row value for a formelement
     * for comparison againstthe current value to determine
     * if a rowspan calculation needs to be made.
     *
     * @var array
     */
    protected $rowSpanValues = array();

    /**
     * Holds the rowcount for the view version of the table.
     *
     * @var array
     */
    protected $rowCount = 0;

    /**
     * Holds the rowcount for the edit version of the table.
     *
     * @var array
     */
    protected $formRowCount = 0;

    /**
     * This string holds any additional information to be
     * appended to the section-break line just before a table,
     * such as last change date or href to an anchor tag.
     *
     * @var string
     */
    protected $sectionBreakAppend = '';

    /**
     * This string holds any additional information to be
     * appended to the section-break line just before a table,
     * such as last change date or href to an anchor tag.
     *
     * @var string
     */
    protected $formSectionBreakAppend = '';

    /**
     * Contains the HTML for the editable version of the form
     * header cells.
     *
     * @var string, HTML
     */
    protected $formTableHeaderCells = '';

    /**
     * Contains the HTML for the viewOnly version of the form
     * header cells.
     *
     * @var string, HTML
     */
    protected $tableHeaderCells = '';

    /**
     * @param string $tableTitle
     * @param string $tableId
     */
    public function __construct($tableTitle='',$tableId='')
    {
        $this->tableTitle = $tableTitle;
        $this->tableId = $tableId;
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
     * @param string $html
     */
    public function appendFormCustomHtml($html = '')
    {
        $this->formTableSource .= $html;
    }

    /**
     * @param string $html
     */
    public function appendCustomHtml($html = '')
    {
        $this->tableSource .= $html;
    }

   /**
    * @param string $html
    */
    public function appendFormCustomTableCells($html = '')
    {
        $this->currentFormRowTableCells .= $html;
    }

    /**
    * @param string $html
     */
    public function appendCustomTableCells($html = '')
    {
        $this->currentRowTableCells .= $html;
    }

   /**
    * @param string $html
    */
    public function appendFormCustomTableCellContent($html = '')
    {
        if ($this->editPrivileges)
        {
            if (substr($this->currentFormRowTableCells, -5) == "</td>")
            {
                $this->currentFormRowTableCells =
                    substr($this->currentFormRowTableCells, 0, -5)
                    . $html
                    . "</td>";
            }
            else
            {
                throw new PresentationExceptionConfigurationError
                (
                    'Error attempting to append data to non-existent TD node'
                    . ' HtmlTable::appendFormCustomTableCellContent.'
                );
            }
        }
    }

    /**
    * @param string $html
     */
    public function appendCustomTableCellContent($html = '')
    {
        if (substr($this->currentRowTableCells, -5) == "</td>")
        {
            $this->currentRowTableCells =
                substr($this->currentRowTableCells, 0, -5)
                . $html
                . "</td>";
        }
        else
        {
            throw new PresentationExceptionConfigurationError
            (
                'Error attempting to append data to non-existent TD node'
                . ' HtmlTable::appendCustomTableCellContent.'
            );
        }
    }

    /**
     * [TODO: Rename this to appendFormTableRow]
     * @param string $viewEdit Values: view or edit if only want one kind of table.
     */
    public function appendTableRow($viewEdit = '')
    {
        if
        (
            $this->currentRowTableCells == ""
            && $this->currentFormRowTableCells == ""
        )
        {
            $this->appendFormTableCells($viewEdit);
        }

        $tableRowProperties = "";
        if ($this->formConfiguration != '')
        {
            //-----------------------------------------
            //LOOP THROUGH CUSTOM TABLE ROW PROPERTIES
            //-----------------------------------------
            if (isset($this->formConfiguration->tableRowProperties))
            {
                $tableRowPropertiesChildren = $this->formConfiguration->tableRowProperties->children();
                foreach ($tableRowPropertiesChildren as $attribute=>$value)
                {
                    //CHECK IF THERE IS A CONDITIONAL ON
                    //WHETHER TO SET THE TABLE CELL PROPERTY
                    $setProperty = true;
                    $propertyNotSet = true;
                    if (isset($value->ifXpath))
                    {
                        foreach ($value->ifXpath as $ifXpath)
                        {
                             if
                             (
                                 SimpleXml::getSimpleXmlItem
                                 (
                                     $this->tableData
                                     ,$ifXpath
                                 )
                             )
                             {
                                 $value = $ifXpath["value"];
                                 $propertyNotSet = false;
                                 break;
                             }
                        }

                         if ($propertyNotSet)
                         {
                              if (isset($value->else))
                              {
                                  $value = $value->else;
                              }
                              else
                              {
                                  $setProperty = false;
                              }
                         }
                    }

                    if ($setProperty)
                    {
                        $tableRowProperties .= " " . $attribute . "=\"" . $value . "\"";
                    }
                }
            }
        }

        if ($viewEdit != 'edit')
        {
            $this->tableSource .= "<tr{$tableRowProperties}>" . $this->currentRowTableCells . "</tr>";
        }

        if ($viewEdit != 'view')
        {
            $this->formTableSource .= "<tr{$tableRowProperties}>" . $this->currentFormRowTableCells . "</tr>";
        }

        //RESET TABLE CELLS AND COLUMN SPANS
        $this->currentRowTableCells = "";
        $this->currentFormRowTableCells = "";

        $this->currentRowColumnSpan = 0;
        $this->currentFormRowColumnSpan = 0;
    }

    /**
     * Sets all properties and methods for a formfield.
     * 
     * [TODO: Move this to the factory.]
     * 
     * @param FormField $formfield
     */
    protected function setFormfield($formfield)
    {
        $htmlFormField = FormFieldFactory::create($formfield->type);
        $htmlFormField->setLabel($formfield->label);
        $htmlFormField->setId($formfield->id . $this->currentRow);
        $htmlFormField->setData($formfield, $this->tableData, $this->pageObjectsXml);

        //LOOP THROUGH CUSTOM METHODS
        if (isset($formfield->methods))
        {
            foreach ($formfield->methods->children() as $method=>$input)
            {
                $htmlFormField->$method
                (
                    str_replace
                    (
                         "%CURRENTROW%"
                         ,$this->currentRow
                         ,$input
                    )
                );
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
                              $this->tableData
                              ,$value
                          )
                )
                {
                     $formFieldDisplayValue .= $value["delimeter"]
                                         . SimpleXml::getSimpleXmlItem
                                         (
                                             $this->tableData
                                             ,$value
                                         );
                }
            }
            $htmlFormField->setDefaultValue($formFieldDisplayValue);
        }
        elseif (SimpleXml::getSimpleXmlItem($this->tableData,$formfield->valueXpath))
        {
            $htmlFormField->setDefaultValue
            (
                (string) SimpleXml::getSimpleXmlItem
                (
                    $this->tableData
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

        return $htmlFormField;
    }

	/**
     * @param string $viewEdit Values: view or edit if only want one kind of table.
     */
    public function appendFormTableCells($viewEdit = '')
    {
        if ($this->formConfiguration == '')
        {
            throw new PresentationExceptionConfigurationError
            (
                'A form configuration is required for HtmlForm::appendTableRow.'
            );
        }

        // ADD EMPTY ELEMENT IF NO FORM DATA
        if (count((array)$this->tableData) == 0)
        {
            $this->tableData = new SimpleXMLElement('<none></none>');
        }

        // ADD EMPTY ELEMENT IF NO PAGE DATA
        if (count((array)$this->pageObjectsXml) == 0)
        {
            $this->pageObjectsXml = new SimpleXMLElement('<none></none>');
        }

        //LOOP THOUGH FORM ELEMENTS
        foreach($this->formConfiguration->formfield as $formfield)
        {
            //---------------------------------
            // GET FORM DATA VALUE
            //---------------------------------
            $showElement = true;
            if (isset($formfield->security))
            {
                $action = "get" . $formfield->security . "Privileges";
                $showElement = Security::$action
                               (
                                   PageConfiguration::getCurrentPageSecurityCode()
                                   ,ApplicationSession::getValue('currentOrgId')
                               );
            }

            if ($showElement)
            {
                $htmlFormField = $this->setFormfield($formfield);

                //-----------------------------------------
                //LOOP THROUGH CUSTOM TABLE CELL PROPERTIES
                //-----------------------------------------
                $tableCellPropertiesView = "";
                $tableCellPropertiesEdit = "";
                if (isset($formfield->tableCellProperties))
                {
                    $tableCellPropertiesChildren = $formfield->tableCellProperties->children();
                    foreach ($tableCellPropertiesChildren as $attribute=>$value)
                    {
                        //CHECK IF THERE IS A CONDITIONAL ON
                        //WHETHER TO SET THE TABLE CELL PROPERTY
                        $setProperty = true;
                        $propertyNotSet = true;
                        if (isset($value->ifXpath))
                        {
                            foreach ($value->ifXpath as $ifXpath)
                            {
                                 if
                                 (
                                     SimpleXml::getSimpleXmlItem
                                     (
                                         $this->tableData
                                         ,$ifXpath
                                     )
                                 )
                                 {
                                     $value = $ifXpath["value"];
                                     $propertyNotSet = false;
                                     break;
                                 }
                            }

                             if ($propertyNotSet)
                             {
                                  if (isset($value->else))
                                  {
                                      $value = $value->else;
                                  }
                                  else
                                  {
                                      $setProperty = false;
                                  }
                             }
                        }

                        if ($setProperty)
                        {
                            //-------------------------
                            //HANDLE ROWSPAN PROPERTIES
                            //-------------------------
                            if ($attribute == "rowspan")
                            {
                                $rowspan = 0;
                                if
                                (
                                   (
                                       !array_key_exists
                                       (
                                           (string) $formfield->id
                                           ,$this->rowSpanValues
                                       )
                                       ||
                                       $this->rowSpanValues[(string) $formfield->id]
                                       != $htmlFormField->getDefaultValue()
                                   )
                                   ||
                                   (
                                       isset($value->rowspanXpathParent)
                                       &&
                                       (
                                           !array_key_exists
                                           (
                                               (string) $formfield->id . "Parent"
                                               ,$this->rowSpanValues
                                           )
                                           ||
                                           (string) SimpleXml::getSimpleXmlItem
                                           (
                                               $this->tableData
                                               ,$value->rowspanXpathParent
                                           )
                                           !=
                                           $this->rowSpanValues[(string) $formfield->id . "Parent"]
                                       )
                                   )
                                )
                                {
                                    //AQUIRE ROWSPAN VIA A DIRECT COUNT OF
                                    //ELEMENTS IN THE ARRAY THAT MATCH THE
                                    //CURRENT VALUE
                                    if (isset($value->rowspanXpath))
                                    {
                                        if
                                        (
                                            isset($value->rowspanXpath->view)
                                            || isset($value->rowspanXpath->edit)
                                        )
                                        {
                                            $rowspanView = count
                                                       (
                                                           $this->pageObjectsXml->xpath
                                                             (
                                                                 str_replace
                                                                 (
                                                                     "%VALUE%"
                                                                     ,$htmlFormField->getDefaultValue()
                                                                     ,$value->rowspanXpath->view
                                                                 )
                                                             )
                                                        );

                                            $rowspanEdit = count
                                                       (
                                                           $this->pageObjectsXml->xpath
                                                             (
                                                                 str_replace
                                                                 (
                                                                     "%VALUE%"
                                                                     ,$htmlFormField->getDefaultValue()
                                                                     ,$value->rowspanXpath->edit
                                                                 )
                                                             )
                                                        );

                                        }
                                        else
                                        {
                                            $rowspan = count
                                                       (
                                                           $this->pageObjectsXml->xpath
                                                             (
                                                                 str_replace
                                                                 (
                                                                     "%VALUE%"
                                                                     ,$htmlFormField->getDefaultValue()
                                                                     ,$value->rowspanXpath
                                                                 )
                                                             )
                                                        );
                                        }
                                    }
                                    else if (isset($value->rowspanLoopXpath))
                                    {
                                       //--------------------------------
                                       //NEED: VIEW/EDIT ROWSPANLOOPXPATH
                                       //--------------------------------
                                       if (isset($value->rowspanXpathParent))
                                        {
                                            //IF ROWSPAN HAS A COLUMN BEFORE IT WITH A ROWSPAN
                                            //USE THE PARENT VALUE AND LOOP THROUGH IT TO AQUIRE
                                            //ROWPSPAN COUNT
                                            $currentXpath = str_replace
                                                            (
                                                                "%VALUE%"
                                                                ,(string) SimpleXml::getSimpleXmlItem
                                                                 (
                                                                     $this->tableData
                                                                     ,$value->rowspanXpathParent
                                                                 )
                                                                ,$value->rowspanLoopXpath
                                                            );
                                             $this->rowSpanValues[(string) $formfield->id . "Parent"] =
                                                 (string) SimpleXml::getSimpleXmlItem
                                                 (
                                                     $this->tableData
                                                     ,$value->rowspanXpathParent
                                                 );
                                        }
                                        else
                                        {
                                            $currentXpath = str_replace
                                                            (
                                                                "%VALUE%"
                                                                ,$htmlFormField->getDefaultValue()
                                                                ,$value->rowspanLoopXpath
                                                            );
                                        }

                                        $loopValueFound = false;
                                        foreach ($this->pageObjectsXml->xpath($currentXpath) as $loopValue)
                                        {
                                            if ($loopValue == $htmlFormField->getDefaultValue())
                                            {
                                                $rowspan++;
                                                $loopValueFound = true;
                                            }
                                            elseif ($loopValueFound)
                                            {
                                                break;
                                            }
                                        }
                                    }

                                    if
                                    (
                                        isset($value->rowspanXpath->view)
                                        || isset($value->rowspanXpath->edit)
                                    )
                                    {
                                        //--------------------------------
                                        //NEED: VIEW/EDIT ROWSPAN ADJUSTMENTS
                                        //--------------------------------
                                        $tableCellPropertiesView .= " "
                                            . $attribute . "=\""
                                            . $rowspanView . "\"";

                                        $tableCellPropertiesEdit .= " "
                                            . $attribute . "=\""
                                            . $rowspanEdit . "\"";
                                    }
                                    else
                                    {
                                        //ADJUST ROWSPAN IF ADJUSTMENT ATTRIBUTE IS SET
                                        if (isset($value["adjustment"]))
                                        {
                                            $rowspan = $rowspan + intval($value["adjustment"]);
                                        }

                                        $tableCellPropertiesView .= " "
                                            . $attribute . "=\""
                                            . $rowspan . "\"";

                                        $tableCellPropertiesEdit .= " "
                                            . $attribute . "=\""
                                            . $rowspan . "\"";
                                    }

                                    $this->rowSpanValues[(string) $formfield->id] = $htmlFormField->getDefaultValue();
                                }
                                else
                                {
                                    //IF VALUE-BY-ROW HASN'T CHANGED, DON'T WRITE TABLE CELL
                                    $showElement = false;
                                }
                            }
                            //END HANDLE ROWSPAN PROPERTIES
                            else
                            {
                                $tableCellPropertiesView .= " " . $attribute . "=\"" . $value . "\"";
                                $tableCellPropertiesEdit .= " " . $attribute . "=\"" . $value . "\"";
                            }
                        }
                    }
                }
                else
                {
                    $tableCellPropertiesView =  "";
                    $tableCellPropertiesEdit =  "";
                }

                if
                (
                    $showElement
                    && (string) $formfield->label != ''
                    && (string) $formfield->display != 'editonly'
                    && $viewEdit != 'edit'
                )
                {
                    $this->currentRowTableCells .=
                    	"<td class=\"tableform\"{$tableCellPropertiesView}>"
                        . $htmlFormField->getSource(true)
                        . "</td>";
                    $this->currentRowColumnSpan++;
                }

                //If Edit Privileges, Provide Edit Form
                if
                (
                    $this->editPrivileges
                    && (string) $formfield->display != 'viewonly'
                    && $viewEdit != 'view'
                )
                {
                    $htmlFormField->setSource();
                    $htmlFormField->setJavaScriptValidation();

                    if ($showElement)
                    {
                        //DON'T WRITE TABLE CELLS FOR HIDDEN FORMELEMENTS
                        if ((string) $formfield->type == 'Hidden')
                        {
                            //INSERT HIDDEN FIELD WITHIN LAST TD NODE
                            //FOR XHTML COMPLIANCE
                            if (substr($this->currentFormRowTableCells, -5) == "</td>")
                            {
                                $this->currentFormRowTableCells =
                                    substr($this->currentFormRowTableCells, 0, -5)
                                    . $htmlFormField->getSource()
                                    . "</td>";
                            }
                            //IF NO TD CELLS APPENDEDED, PUT PLACEHOLDER
                            else if ($this->currentFormRowTableCells == '')
                            {
                                $this->currentFormRowTableCells .=
                                    "%TD%"
                                    . $htmlFormField->getSource();
                            }
                            else
                            {
                                $this->currentFormRowTableCells .=
                                    $htmlFormField->getSource();
                            }
                        }
                        else
                        {
                            //APPEND NON-TD NESTED CONTENT INTO CURRENT TD NODE
                            if (substr($this->currentFormRowTableCells, 0, 4) == "%TD%")
                            {
                                $this->currentFormRowTableCells = "<td class=\"tableform\""
                                                     . $tableCellPropertiesEdit
                                                     . " id=\""
                                                     . "tableCell"
                                                     . $formfield->id
                                                     . $this->currentRow . "\""
                                                     . ">"
                                                     . substr($this->currentFormRowTableCells,4)
                                                     . $htmlFormField->getSource()
                                                     . "</td>";
                            }
                            else
                            {
                                $this->currentFormRowTableCells .= "<td class=\"tableform\""
                                                     . $tableCellPropertiesEdit
                                                     . " id=\""
                                                     . "tableCell"
                                                     . $formfield->id
                                                     . $this->currentRow . "\""
                                                     . ">"
                                                     . $htmlFormField->getSource()
                                                     . "</td>";
                            }
                            $this->currentFormRowColumnSpan++;
                        }
                    }
                }//EDIT PRIVILEGES CHECK
            }//SHOW ELEMENT CHECK
        }//END LOOP THROUGH FORM ELEMENTS
    }

    /**
     * Appends table header to table
     */
    public function appendTableHeader()
    {
        if
        (
            $this->tableHeaderCells == ""
            && $this->formTableHeaderCells == ""
        )
        {
            $this->appendTableHeaderCells();
        }

        $this->tableSource .= "<tr>" . $this->tableHeaderCells . "</tr>";
        $this->formTableSource .= "<tr>" . $this->formTableHeaderCells . "</tr>";
    }

    /**
     * Builds an HTML table looping through the dataArray
     * provided to it as rows.
     *
     * @param array $dataArray <todo:description>
     */
    public function appendFormTable($dataArray=array())
    {
        $this->appendTableHeader();
        $this->formRowCount = -1;
        foreach ($dataArray as $rowArray)
        {
            if ($rowArray)
            {
                $viewEditDisplay = "";

                $this->formRowCount++;
                $this->setCurrentRow($this->formRowCount);
                $this->setTableData($rowArray);

                if (isset($this->formConfiguration->display))
                {
                    if (isset($this->formConfiguration->display->ifXpath))
                    {
                         if
                         (
                             SimpleXml::getSimpleXmlItem
                             (
                                 $rowArray
                                 ,$this->formConfiguration->display->ifXpath
                             )
                         )
                         {
                             $viewEditDisplay = $this->formConfiguration->display->ifXpath["value"];
                         }
                    }
                    else
                    {
                        $viewEditDisplay = $this->formConfiguration->display;
                    }
                }
                $this->appendTableRow($viewEditDisplay);
            }
        }

        if ($this->formRowCount == -1)
        {
            //ADD BLANK ROW TO EDIT FORM
            $this->formRowCount++;
            $this->setCurrentRow($this->formRowCount);
            $this->appendTableRow();
            //RETURN NO RECORDS FOR VIEW
            $this->tableSource =
            	"<tr><td><div class=\"largeBlue\">"
                . "<p>No " . $this->tableTitle . "</p>"
                . "</div></td></tr>";
        }
    }

    /**
     * Appends table header cells
     */
    public function appendTableHeaderCells()
    {
        if (count((array)$this->formConfiguration) == 0)
        {
            throw new PresentationExceptionConfigurationError
            (
                'A form configuration is required for HtmlTable::appendTableHeader.'
            );
        }

        //LOOP THOUGH FORM ELEMENTS
        foreach($this->formConfiguration->formfield as $formfield)
        {
            $showElement = true;
            if (isset($formfield->security))
            {
                $action = "get".$formfield->security."Privileges";
                $showElement = Security::$action
                               (
                                   PageConfiguration::getCurrentPageSecurityCode()
                                   ,ApplicationSession::getValue('currentOrgId')
                               );
            }

            if
            (
                (string) $formfield->type != 'Hidden'
                && $showElement
            )
            {
                if
                (
                    (string) $formfield->label != ''
                    && (string) $formfield->display != 'editonly'
                )
                {
                    $this->tableHeaderCells .= "<th class=\"tableform\">"
                                    . wordwrap($formfield->label, 20, "<br />")
                                    . "</th>";
                }

                if ((string) $formfield->display != 'viewonly')
                {
                    $this->formTableHeaderCells .= "<th class=\"tableform\">"
                                    . wordwrap($formfield->label, 20, "<br />")
                                    . "</th>";
                }
            }
        }
    }


    /**
     * Gets current column span for the form.
     *
     * @return string
     */
    public function getCurrentFormRowColumnSpan()
    {
        return $this->currentFormRowColumnSpan;
    }

    /**
     * Gets current column span for the view.
     *
     * @return <type> <todo:description>
     */
    public function getCurrentRowColumnSpan()
    {
        return $this->currentRowColumnSpan;
    }

    /**
     * Gets table source.
     *
     * @param boolean $includeTable Include encapsulating table
     * @return string, HTML
     */
    public function getFormTableSource($includeTable=true)
    {
        if ($includeTable)
        {
            $tableFormFields = "";
            //LOOP THOUGH TABLE FORM ELEMENTS
            foreach($this->formConfiguration->tablefield as $formfield)
            {
                $htmlFormField = FormFieldFactory::create($formfield->type);
                if ($formfield->type == "HiddenRowCount")
                {
                    $htmlFormField->setDefaultValue($this->formRowCount);
                }
                $htmlFormField->setId($formfield->id);

                //LOOP THROUGH CUSTOM METHODS
                if (isset($formfield->methods))
                {
                    foreach ($formfield->methods->children() as $method=>$input)
                    {
                        $htmlFormField->$method($input);
                    }
                }
                $htmlFormField->setSource();
                $tableFormFields .= $htmlFormField->getSource();
            }

            return
                "<table class=\"layout\" width=\"100%\">"
                .  "<tr>"
                .    "<td><b>{$this->tableTitle}:"
                .    "<a class=\"noLink\" name=\"{$this->tableId}AnchorEdit\"></a>"
                .    "</b></td>"
                .    "<td width=\"100%\"><hr /></td>"
                .    $this->formSectionBreakAppend
                .  "</tr>"
                ."</table>"
                ."<table class=\"tableform\""
                .    " id=\"{$this->tableId}TableEdit\">"
                .    "<tbody id=\"{$this->tableId}TableTbodyEdit\">"
                .    $this->formTableSource
                .    "</tbody>"
                ."</table>"
                ."<br/>"
                .$tableFormFields;
        }
        else
        {
            return $this->formTableSource;
        }
    }

    /**
     * Gets the table source.
     * @param boolean $includeTable Include encapsulating table.
     * @return <type> <todo:description>
     */
    public function getTableSource($includeTable=true)
    {
        if ($includeTable)
        {
            $classDisplay = "";
            if (strstr($this->tableSource, "largeBlue"))
            {
                $classDisplay = " class=\"layout\"";
            }
            else
            {
                $classDisplay = " class=\"tableform\"";
            }
            
            return
                "<table class=\"layout\" width=\"100%\">"
                .  "<tr>"
                .    "<td><b>{$this->tableTitle}:"
                .    "<a class=\"noLink\" name=\"{$this->tableId}AnchorView\"></a>"
                .    "</b></td>"
                .    "<td width=\"100%\"><hr /></td>"
                .    $this->sectionBreakAppend
                .  "</tr>"
                ."</table>"
                ."<table"
                .    $classDisplay
                .    " id=\"{$this->tableId}TableView\">"
                .    "<tbody id=\"{$this->tableId}TableTbodyView\">"
                .    $this->tableSource
                .    "</tbody>"
                ."</table>"
                ."<br/>";
        }
        else
        {
            return $this->tableSource;
        }
    }

    /**
     * Builds a javascript object to build a new row on the fly.
     *
     * @return string, JavaScript
     */
    public function getJavaScriptCloneRowArray()
    {
        $tdCellsCount = 0;
        $javaScriptCloneRowArray = "var tdCells = new Array();";
        //LOOP THOUGH FORM ELEMENTS
        foreach($this->formConfiguration->formfield as $formfield)
        {
            //BECAUSE HIDDEN ELEMENTS DON'T GET TABLE CELLS
            if ($formfield->type != "Hidden")
            {
                $javaScriptCloneRowArray .= "tdCells[$tdCellsCount]"
                                                    . "=$('"
                                                    . "tableCell"
                                                    . $formfield->id
                                                    . $this->currentRow
                                                    . "').cloneNode(true);";
                $tdCellsCount++;
            }
            else
            {
                $javaScriptCloneRowArray .= "tdCells[$tdCellsCount]"
                                                    . "=$('"
                                                    . $formfield->id
                                                    . $this->currentRow
                                                    . "').cloneNode(true);";
                $tdCellsCount++;
            }
        }
        return $javaScriptCloneRowArray;
    }

    /**
     * Get just the table hr tag and title.
     *@param $viewEdit view or edit depending on what's needed.
     * @return string
     */
    public function getSectionBreak($viewEdit = '')
    {
        if ($viewEdit == 'edit')
        {
            return
                "<table class=\"layout\">"
                .  "<tr>"
                .    "<td><b>{$this->tableTitle}:</b></td>"
                .    "<td width=\"100%\"><hr /></td>"
                .    $this->formSectionBreakAppend
                .  "</tr>"
                ."</table>";
        }
        else
        {
            return
                "<table class=\"layout\">"
                .  "<tr>"
                .    "<td><b>{$this->tableTitle}:</b></td>"
                .    "<td width=\"100%\"><hr /></td>"
                .    $this->sectionBreakAppend
                .  "</tr>"
                ."</table>";
        }
    }

    /**
     * Get the table id.
     *
     * @return string
     */
    public function getTableId()
    {
        return $this->tableId . "TableEdit";
    }

    /**
     * Get the TBODY id.
     *
     * @return string
     */
    public function getTbodyId()
    {
        return $this->tableId . "TableTbodyEdit";
    }

    /**
     * Sets the current row identifier.
     *
     * @param string $currentRow unique identifier for
     * the current row.
     */
    public function setCurrentRow($currentRow = '')
    {
        $this->currentRow = $currentRow;
    }

    /**
     * Sets true/false edit privileges, if false, only builds view.
     *
     * @param boolean $editPrivileges
     */
    public function setEditPrivileges($editPrivileges = false)
    {
        $this->editPrivileges = $editPrivileges;
    }

    /**
     * Sets the form configuration.
     *
     * @param SimpleXmlObject $formConfiguration
     */
    public function setFormConfiguration($formConfiguration)
    {
        $this->formConfiguration = $formConfiguration;
        $this->tableHeaderCells = "";
        $this->formTableHeaderCells = "";
    }

    /**
     * Sets the table data.
     *
     * @param SimpleXmlObject $tableData
     */
    public function setTableData($tableData = array())
    {
        $this->tableData = $tableData;
    }

    /**
     * @param string $tableId
     */
    public function setTableId($tableId = '')
    {
        $this->tableId = $tableId;
    }

    /**
     * @param string $tableTitle
     */
    public function setTableTitle($tableTitle = '')
    {
        $this->tableTitle = $tableTitle;
    }

    /**
     * @param SimpleXmlObject $pageObjectsXml
     */
    public function setPageObjectsXml($pageObjectsXml = array())
    {
        $this->pageObjectsXml = $pageObjectsXml;
    }

    /**
     * @param string $tableTitle
     */
    public function setSectionBreakAppend($text = '',$viewEdit = '')
    {
        if ($viewEdit == '')
        {
            $this->sectionBreakAppend .= "<td><b>$text</b></td>";
            $this->formSectionBreakAppend .= "<td><b>$text</b></td>";
        }
        else
        {
            if ($viewEdit == 'view')
            {
                $this->sectionBreakAppend .= "<td><b>$text</b></td>";
            }
            else if ($viewEdit == 'edit')
            {
                $this->formSectionBreakAppend .= "<td><b>$text</b></td>";
            }
        }
    }
}
