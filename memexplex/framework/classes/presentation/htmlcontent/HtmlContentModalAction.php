<?php

/**
 * Generic modal window for lots of common functions like delete, etc.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see HtmlContent
 * @see HtmlContentInterface
 */
class HtmlContentModalAction extends HtmlContent
implements HtmlContentInterface
{

    /**
     * Sets the HTML Source.
     */
    public function setSource()
    {
        $buttonSet      = null;
        $prompt         = null;
        $formName       = null;
        $formFieldToSet = null;
        $formFieldValue = null;
        if (isset($_GET['prompt']))
        {
            $prompt         = $_GET['prompt'];
        }
        if (isset($_GET['buttonSet']))
        {
            $buttonSet      = $_GET['buttonSet'];
        }
        if (isset($_GET['formName']))
        {
            $formName       = $_GET['formName'];
        }
        if (isset($_GET['formFieldToSet']))
        {
            $formFieldToSet = $_GET['formFieldToSet'];
        }
        if (isset($_GET['formFieldValue']))
        {
            $formFieldValue = $_GET['formFieldValue'];
        }

        $this->source = <<<EOD
<script type="text/javascript">
    document.body.parentNode.style.backgroundColor = '#ccffff';
	document.body.style.backgroundColor = '#ccffff';
    function handleClick(action)
    {
// Positive reponse from user. See if parameters were passed in. If so, set the value of the input control
// on the form of the calling program to the parameter value. Submit the form.
        if (action == "Yes" || action == "Delete" || action == "Sign" || action == "SaveAndAddAnother" || action == "Continue" || action == "OK" || action == "Remove")
        {
EOD;
        if ($formFieldToSet)
        {
            $this->source .= <<<EOD
            parent.$('{$formFieldToSet}').value = '{$formFieldValue}';
EOD;
            if (isset($_GET["dropdown"]))
            {
                $this->source .= <<<EOD
            parent.$('hidRedirAssetTypeId').value = $('sltAssetTypes').value;
            parent.$('hidRedirAssetType').value = $('sltAssetTypes').options[$('sltAssetTypes').selectedIndex].text;
EOD;
            }
        }
        $this->source .= <<<EOD
        parent.FormValidation('{$formName}');
    }
    else if (action == "SaveAndReturn")
    {
        parent.FormValidation('{$formName}');
    }
// Close this window.
// Negative response from user. Do nothing. This window will be closed below.
// if (action == "Cancel" || action == "No" )
        bWinCreated = false;
        parent.hidePopWin(false);
    }
</script>
<form name="frmModalForm">
<br/><br/>
     <div style="text-align:center;">
        <font class="largeBlue">{$prompt}</font><br/><br/>
EOD;

       switch ($buttonSet)
       {
/**
 * [TODO: REPLACE NUMBERS WITH DESCRIPTIVE KEYWORDS]
 */
            case 'DeleteConfirmation':
                $this->source .=
        '<input type="button" value="      Cancel      " onclick="handleClick(\'Cancel\');"/>
         <input type="button" value="      Delete      " onclick="handleClick(\'Delete\');"/>';
                break;
            case 'AddMissionEvent':
                $this->source .=
        '<input type="button" value="Save - Next Page    " onclick="handleClick(\'SaveAndReturn\');"/><br/>';
                if (isset($_GET["dropdown"]))
                {
                    $this->source .=
        '<input type="button" value=" Save - Add Another " onclick="handleClick(\'SaveAndAddAnother\');"/>
        <select NAME="sltAssetTypes" OnChange="handleClick(\'SaveAndAddAnother\');">
        </select><br/>';
                }
                $this->source .=
        '<input type="button" value="      Cancel        " onclick="handleClick(\'Cancel\');"/><br/>';
                break;
/**
            case '2':
                $this->source .=
        '<input type="button" value="       No        " onclick="handleClick(\'No\');"/>
         <input type="button" value="       Yes       " onclick="handleClick(\'Yes\');"/>';
                break;
            case '3':
                $this->source .=
        '<input type="button" value="Save & Add Another" onclick="handleClick(\'SaveAndAddAnother\');"/>
         <input type="button" value="  Save & Return   " onclick="handleClick(\'SaveAndReturn\');"/>
         <input type="button" value="      Cancel      " onclick="handleClick(\'Cancel\');"/>';
                break;
            case '4':
                $this->source .=
        '<input type="button" value="      Cancel      " onclick="handleClick(\'Cancel\');"/>
         <input type="button" value="       Sign       " onclick="handleClick(\'Sign\');"/>';
                break;
            case '6':
                $this->source .=
        '<input type="button" value="       Cancel       " onclick="handleClick(\'Cancel\');"/>
         <input type="button" value="      Continue      " onclick="handleClick(\'Continue\');"/>';
                break;
            case '7':
                $this->source .=
        '<input type="button" value="       Cancel       " onclick="handleClick(\'Cancel\');"/>
         <input type="button" value="         OK         " onclick="handleClick(\'OK\');"/>';
                break;
            case '8':
                $this->source .=
        '<input type="button" value="       Cancel       " onclick="handleClick(\'Cancel\');"/>
         <input type="button" value="       Remove       " onclick="handleClick(\'Remove\');"/>';
                break;
            case '9':
                $this->source .=
        '<input type="button" value="Save & Add Another  " onclick="handleClick(\'SaveAndAddAnother\');"/>
         <input type="button" value="Save - Next Page    " onclick="handleClick(\'SaveAndReturn\');"/>
         <input type="button" value="      Cancel        " onclick="handleClick(\'Cancel\');"/>';
                break;
            case '10':
                $this->source .=
        '<input type="button" value="<%=Session.Contents("s_modal_button_1")%>" SIZE="<%=Session.Contents("s_modal_button_size")%>" onclick="handleClick(\'SaveAndAddAnother\');"/><br/>
         <input type="button" value="<%=Session.Contents("s_modal_button_2")%>" SIZE="<%=Session.Contents("s_modal_button_size")%>" onclick="handleClick(\'SaveAndReturn\');"/><br/>
         <input type="button" value="<%=Session.Contents("s_modal_button_3")%>" SIZE="<%=Session.Contents("s_modal_button_size")%>" onclick="handleClick(\'Cancel\');"/><br/>';
                break;
*/
            default:
                $this->source .= "An Error Occurred Retrieving actions.";
       }
        $this->source .= <<<EOD
    </div>
</form>
EOD;

    }

}
