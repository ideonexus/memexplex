<?php

/**
 * Builds a datefield with calendar icon and validation.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormField
 * @see FormFieldInterface
 */
class FormFieldDate extends FormField
implements FormFieldInterface
{
    /**
     * @var string Usually today's date.
     */
    protected $defaultDate;

    /**
     * @var string GMT or local.
     */
    protected $zuluLocal = 'zulu';

    /**
     * @var bool Whether to validate the entry with javascript.
     */
    protected $validateField = true;

    /**
     * @var bool If true, prevents user entering future dates.
     */
    protected $futureDateJavaScriptValidation = false;

    /**
     * @var string Set to false if date is mandatory entry.
     */
    protected $dateNotRequiredJavascriptValidation = 'true';

    /**
     * @var array Functions for validating cloned fields.
     */
    protected static $cloneJavascriptValidationFunctions = array();

    /**
     * @param string $defaultDate
     */
    public function setValidateFieldFalse()
    {
        $this->validateField = false;
    }

    /**
     * @param string $defaultDate
     */
    public function setDefaultValue($defaultValue = '')
    {
        if ($defaultValue == '')
        {
            $this->defaultValue = $defaultValue;
        }
        else
        {
            $this->defaultValue = date
                                 (
                                     "m/d/Y"
                                     ,strtotime
                                     (
                                         $defaultValue
                                     )
                                 );
        }
    }

    /**
     * @param string $defaultDate
     */
    public function setDefaultDate($defaultDate = '')
    {
        if ($defaultValue == '')
        {
            $this->defaultDate = Time::getZuluDateFormFormat();
        }
        else
        {
        $this->defaultDate = date
                             (
                                 "m/d/Y"
                                 ,strtotime
                                 (
                                     $defaultDate
                                 )
                             );
        }
    }

    /**
     * @param string $zuluLocal
     */
    public function setZuluLocal($zuluLocal = 'zulu')
    {
        $this->zuluLocal = $zuluLocal;
    }

    /**
     * @param string $futureDateJavaScriptValidation
     */
    public function setFutureDateJavaScriptValidation($futureDateJavaScriptValidation = false)
    {
        $this->futureDateJavaScriptValidation = $futureDateJavaScriptValidation;
    }

    /**
     * @param string $dateNotRequiredJavascriptValidation
     */
    public function setDateNotRequiredJavascriptValidation($dateNotRequiredJavascriptValidation = 'true')
    {
        $this->dateNotRequiredJavascriptValidation = (string) $dateNotRequiredJavascriptValidation;
    }

    /**
     * Sets the source propery to the HTML string.
     */
    public function setSource()
    {

        if ($this->defaultValue == '')
        {
            $this->defaultValue = date("m/d/Y");
        }
        
        if ($this->defaultDate == '')
        {
            if ($this->zuluLocal == 'zulu')
            {
                $this->defaultDate = Time::getZuluDateFormFormat();
            }
        }

        $this->source .=  "<span id=\"alert{$this->id}\"></span>"
                         . "<input"
                         . " type=\"text\""
                         . " name=\"{$this->id}\""
                         . " id=\"{$this->id}\""
                         . " value=\"{$this->defaultValue}\""
                         . " size=\"8\""
                         . " title=\"Enter Last Met Date\""
                         . " maxlength=\"10\""
                         . " onkeydown=\"DateRoller(event,this,'{$this->defaultDate}')\""
                         . " disabled=\"disabled\""
                         . " />"
                         . "<a name=\"{$this->id}\""
                         . " href=\"javascript:doNothing()\""
                         . " onclick=\"setDefaultDate('{$this->defaultDate}');"
                         . "setDateField($(this.name));"
                         . "openCalendar('"
                         . ApplicationSession::getValue('CURRENT_APPLICATION_DIRECTORY','all')
                         . "');\">"
                         . "<img src=\""
                         . ROOT_FOLDER
                         . "framework/images/calendar.gif\""
                         . " width=\"16\""
                         . " height=\"16\""
                         . " border=\"0\""
                         . " alt=\"Click to view calendar.\"></a>";

    }

    /**
     * JavaScript validation for the date field.
     */
    public function setJavaScriptValidation()
    {

        $this->source .= "<script type=\"text/javascript\">";
        if (!defined("DATE_FIELD_TODAYS_DATE"))
        {
            $this->source .= "var todaysDate = '" . Time::getZuluDateFormFormat() . "';";
            define("DATE_FIELD_TODAYS_DATE",true);
        }

        //SOME CLONABLE FIELDS ARE HIDDEN AND
        //SHOULD NOT BE THEMSELVES VALIDATED
        $rowOffset = "";
        if ($this->validateField)
        {
            $this->source .= "var validate{$this->id} = function ()"
                             . "{"
                             .     "if ((CheckDate(\"{$this->id}\",1,{$this->dateNotRequiredJavascriptValidation}) == false))"
                             .     "{"
                             .         "bSubmitForm = false;"
                             .         "highlightFormError(\"{$this->id}\");"
                             .         "processFormAlert(\"Please enter a valid {$this->label}. (mm/dd/yyyy)\");"
                             .     "}";

            if ($this->futureDateJavaScriptValidation)
            {

                $this->source .=   "else if (futureDateCheck(\"{$this->id}\", \"{$this->defaultDate}\") == false)"
                             .     "{"
                             .         "bSubmitForm = false;"
                             .         "highlightFormError(\"{$this->id}\");"
                             .         "processFormAlert(\"{$this->label} cannot be a future date.\");"
                             .     "}";
            }

            $this->source .=      "else"
                             .     "{"
                             .         "dehighlightFormError(\"{$this->id}\");"
                             .     "}"
                             . "};"
                             //ON AJAX CALL, WHEN BODY CONTENT IS REFRESHED
                             //RESUBSCRIBE THE FORM VALIDATION
                             . "var destroy{$this->id}DateValidation = function ()"
                             . "{"
                             .     "validate{$this->id} = null;"
                             . "};"
                             . "addLoadEvent(function()"
                             . "{"
                             // SET TIMEOUT ALLOWS FormValidationObserver TO
                             // LOAD BEFORE ATTEMPTING TO SUBSCRIBE TO IT.
                             .     "setTimeout('FormValidationObserver.subscribe(validate{$this->id})',500);"
                             .     "setTimeout('fillInTableCallbackObserver.subscribe(destroy{$this->id}DateValidation)',500);"
                             . "});";
            $rowOffset = "+1";
        }

        if ($this->clonable)
        {
            //IF DATE FIELD IS A CLONABLE ROW OF A CLONABLE ROW
            if (strpos($this->id,"_"))
            {
                $clonableFieldName = substr($this->id,0,(strpos($this->id,"_")+1));
                $clonableFieldRow  = preg_replace("/[_]/","",strstr($this->id,"_"));
            }
            else
            {
                $clonableFieldName = preg_replace("/[0-9]/","",$this->id);
                $clonableFieldRow  = preg_replace("/[a-zA-Z]/","",$this->id);
            }

            if (!in_array($clonableFieldName,self::$cloneJavascriptValidationFunctions))
            {
                $this->source .= "var validate{$clonableFieldName}Clones = function ()"
                                 ."{"
                                 .     "fieldName = \"{$clonableFieldName}\";"
                                 .     "fieldRow  = ({$clonableFieldRow}{$rowOffset});"
                                 .     "fieldExists = true;"
                                 .     "while (fieldExists == true)"
                                 .     "{"
                                 //DON'T VALIDATE HERE IF A FUNCTION
                                 //ALREADY EXISTS TO VALIDATE FIELD
                                 .         "if (eval(\"typeof validate\" + fieldName + fieldRow + \" != 'function'\"))"
                                 .         "{"
                                 .             "if ($(fieldName + fieldRow) != null)"
                                 .             "{"
                                 .                 "if ((CheckDate(fieldName + fieldRow,1,{$this->dateNotRequiredJavascriptValidation}) == false))"
                                 .                 "{"
                                 .                     "bSubmitForm = false;"
                                 .                     "highlightFormError(fieldName + fieldRow);"
                                 .                     "processFormAlert(\"Please enter a valid {$this->label}. (mm/dd/yyyy)\");"
                                 .                 "}";

                if ($this->futureDateJavaScriptValidation)
                {
                    $this->source .=               "else if (futureDateCheck(fieldName + fieldRow, \"{$this->defaultDate}\") == false)"
                                 .                 "{"
                                 .                      "bSubmitForm = false;"
                                 .                      "highlightFormError(fieldName + fieldRow);"
                                 .                      "processFormAlert(\"{$this->label} cannot be a future date.\");"
                                 .                  "}";
                }

                $this->source .=                    "else"
                                 .                  "{"
                                 .                      "dehighlightFormError(fieldName + fieldRow);"
                                 .                   "}"
                                 .              "}"
                                 .              "else"
                                 .              "{"
                                 .                   "fieldExists = false;"
                                 .               "}"
                                 .         "}"
                                 .         "fieldRow++;"
                                 .     "}"
                                 . "};"
                                 //ON AJAX CALL, WHEN BODY CONTENT IS REFRESHED
                                 //RESUBSCRIBE THE FORM VALIDATION
                                 . "var destroy{$clonableFieldName}ClonesDateValidation = function ()"
                                 . "{"
                                 .     "validate{$clonableFieldName}Clones = null;"
                                 . "};"
                                 . "addLoadEvent(function()"
                                 . "{"
                                 .     "setTimeout('FormValidationObserver.subscribe(validate{$clonableFieldName}Clones)',500);"
                                 .     "setTimeout('fillInTableCallbackObserver.subscribe(destroy{$clonableFieldName}ClonesDateValidation)',500);"
                                 . "});";

                self::$cloneJavascriptValidationFunctions[] = $clonableFieldName;
            }
        }

        $this->source .= "</script>";
    }

    /**
     * Gets the source.
     */
    public function getSource($view=false)
    {
        if ($view)
        {
            if ($this->defaultValue != "")
            {
                $returnDisplayDate = strtoupper
                                   (
                                       date
                                       (
                                           "d M Y"
                                           ,strtotime
                                           (
                                               $this->defaultValue
                                           )
                                       )
                                   );
                if ($this->zuluLocal == "zulu")
                {
                    $returnDisplayDate .= " Z";
                }
                return $returnDisplayDate;
            }
            else
            {
                return "-&nbsp;-";
            }
        }
        else
        {
            //INCLUDE JAVASCRIPT DATE FUNCTIONS WHEN EDITABLE
            JavaScript::addJavaScriptInclude("dateFunctions");
            return $this->source;
        }
    }

}
