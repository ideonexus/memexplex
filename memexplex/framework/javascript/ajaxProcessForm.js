/////////////////////////////////////////////////////
//
// To change this template, choose Tools | Templates
// and open the template in the editor.
//
//ajaxProcessForm.js
//
//Requires:
//qmdJavaScriptFunctions.js
//
/////////////////////////////////////////////////////

function highlightFormError(formFieldId)
{
	if ($('alert' + formFieldId))
	{
	    $('alert' + formFieldId).innerHTML = "<span class=\"ExclamationRed\"><b>&nbsp;!&nbsp</b></span>";
	    $(formFieldId).style.backgroundColor="#FF9999";
	}
}

function dehighlightFormError(formFieldId)
{
	if ($('alert' + formFieldId))
	{
	    $('alert' + formFieldId).innerHTML = "";
	    $(formFieldId).style.backgroundColor="#FFFFFF";
	}
}

function dehighlightAllFormErrors(formName)
{
    for(i=0;i<document.forms[formName].elements.length;i++)
    {
        if(document.forms[formName].elements[i].id != '')
        {
            if
            (
                   document.forms[formName].elements[i].type == "text"
                   || document.forms[formName].elements[i].type == "textarea"
                   || document.forms[formName].elements[i].type == "checkbox"
                   || document.forms[formName].elements[i].type == "select-one"
                   || document.forms[formName].elements[i].type == "password"
            )
            {
                if ($('alert' + document.forms[formName].elements[i].id) != null)
                {
                    $('alert' + document.forms[formName].elements[i].id).innerHTML = "";
                    $(document.forms[formName].elements[i].id).style.backgroundColor="#FFFFFF";
                }
            }
        }
    }
}

var sPageFilter;
function captureFilterValues()
{
    sPageFilter = "";
    //Grab all Filter Elements and Values and Submit in Query String
    if (typeof document.forms['reportFilter'] != 'undefined')
    {
        for(i=0;i<document.forms['reportFilter'].elements.length;i++)
        {
            if
            (
            	document.forms['reportFilter'].elements[i].type == "text" 
            	|| document.forms['reportFilter'].elements[i].type == "textarea" 
            	|| document.forms['reportFilter'].elements[i].type == "hidden"
            	|| document.forms['reportFilter'].elements[i].type == "radio"
                || document.forms['reportFilter'].elements[i].type == "password"
            )
            {
                sPageFilter = sPageFilter + "&" + document.forms['reportFilter'].elements[i].id + "=" + encodeURI(document.forms['reportFilter'].elements[i].value);
            }
            else if(document.forms['reportFilter'].elements[i].type == "checkbox")
            {
                if (document.forms['reportFilter'].elements[i].checked)
                {
                    sPageFilter = sPageFilter + "&" + document.forms['reportFilter'].elements[i].id + "=" + document.forms['reportFilter'].elements[i].value;
                }
            }
            else if(document.forms['reportFilter'].elements[i].type == "select-one")
            {
                //CHECK TO MAKE SURE THERE ARE OPTIONS IN THE SELECT
                if (document.forms['reportFilter'].elements[i].options[0] != null)
                {
                    sPageFilter = sPageFilter + "&" + document.forms['reportFilter'].elements[i].id + "=" + document.forms['reportFilter'].elements[i].options[document.forms['reportFilter'].elements[i].options.selectedIndex].value;
                }
            }
        }
    }
    return sPageFilter;
}

function captureFormValues(formName,modifier)
{
    sFormValues = "";
    //Grab all Filter Elements and Values and Submit in Query String
    if (typeof document.forms[formName] != 'undefined')
    {
        for(i=0;i<document.forms[formName].elements.length;i++)
        {
            if(document.forms[formName].elements[i].id != '')
            {
                if
                (
                	document.forms[formName].elements[i].type == "text" 
                	|| document.forms[formName].elements[i].type == "textarea" 
                	|| document.forms[formName].elements[i].type == "hidden"
                	|| document.forms[formName].elements[i].type == "radio"
                    || document.forms[formName].elements[i].type == "password"
                )
                {
                    //encodeURI doesn't convert "&", which is a delimter, so convert that too.
                    sFormValues = sFormValues + "&" + modifier + document.forms[formName].elements[i].id + "=" + encodeURI(document.forms[formName].elements[i].value.replace(/&/g, "%26"));
                }
                else if(document.forms[formName].elements[i].type == "checkbox")
                {
                    if (document.forms[formName].elements[i].checked == true)
                    {
                        sFormValues = sFormValues + "&" + modifier + document.forms[formName].elements[i].id + "=" + document.forms[formName].elements[i].value;
                    }
                    else
                    {
                        sFormValues = sFormValues + "&" + modifier + document.forms[formName].elements[i].id + "=";
                    }
                }
                else if(document.forms[formName].elements[i].type == "select-one")
                {
                    //CHECK TO MAKE SURE THERE ARE OPTIONS IN THE SELECT
                    if (document.forms[formName].elements[i].options[0] != null)
                    {
                        sFormValues = sFormValues + "&" + modifier + document.forms[formName].elements[i].id + "=" + document.forms[formName].elements[i].options[document.forms[formName].elements[i].options.selectedIndex].value;
                    }
                }
            }
        }
    }
    return sFormValues;
}

var formValuesOnLoad = '';
var formValuesOnSubmit = '';
var formOriginalValues = '';
function captureFormValuesOnLoad(formName)
{
    formValuesOnLoad = captureFormValues(formName,'');
    formOriginalValues = captureFormValues(formName,'original');
}

function deltaCheck(formName)
{
   formValuesOnSubmit = captureFormValues(formName,'');
   if (formValuesOnLoad == formValuesOnSubmit)
   {
       return false;
   }
   else
   {
       return true;
   }
}

function ajaxProcessForm(formName)
{
    $("errorDisplaySpan").innerHTML = "";
    //$('dataTableAnchor').focus();
    document.forms['report'].hidLoadingState.value = "processing";

    application = document.forms['report'].elements['application'].value;
    pageCode = document.forms['report'].elements['pageCode'].value;

    sFormValues = captureFormValues(formName,'');
    sPageFilter = captureFilterValues();

    //Grab all Variables from the Querystring ("GET" variables,
    //since they are not transfered through the AJAX call automatically)
    //http://www.tek-tips.com/faqs.cfm?fid=5442
    var qparts = window.location.href.split("/");
    if (qparts[qparts.length-1].indexOf('=') != -1)
    {
        sFormValues = sFormValues + "&" + qparts[qparts.length-1];
    }

    s_timer = 0;
    loadingDisplay();
    
    //CLEAR PREVIOUS FORM ERRORS
    dehighlightAllFormErrors(formName);

    //This will call the dynamic web page and pass along my data.
    //It is a PHP page, which will run server-side.  The second
    //parameter indicates what JavaScript function should trigger
    //when the callback is done
    getContent(applicationRootFolder + "framework/api/processForm.php", "application=" + application + "&pageCode=" + pageCode + sFormValues + formOriginalValues, 'processFormCallback');
}

var ProcessFormSuccessObserver;
addLoadEvent(function()
{
	ProcessFormSuccessObserver = new Observer;
});

function processFormCallback(resultContent)
{
    //This is called when the Callback is done!
    //So the callback gets the data and now we say what to do with it.
    if (typeof resultContent != 'undefined')
    {
        //RESET JAVASCRIPT BASED ON RETURNED SCRIPTS
        var jsblock = "";
        var content = unescape(resultContent);
        var jsblock = assembleJavaScriptFromString(content);

        if (content.indexOf("REDIRECT::") != -1)
        {
            //REDIRECT
            eval(jsblock);
        }
        //SUCCESSFULLY EXECUTED TRANSACTION
        else if (content.indexOf("SUCCESS::") != -1)
        {
            $("errorDisplaySpan").innerHTML = content.replace(/SUCCESS::/, "");
            //FLASH TEXT
            eval(jsblock);
            FormValidationObserver.unsubscribeall();
            document.forms['report'].hidLoadingState.value = "complete";
            ProcessFormSuccessObserver.execute();
            ajaxGetHTML(sPageFilter);
        }
        //ERROR AS THE RESULT OF PROCESSING THE FORM
        else if (content.indexOf("ERROR::") != -1)
        {
            $("errorDisplaySpan").innerHTML = content.replace(/ERROR::/, "");
            document.forms['report'].hidLoadingState.value = "complete";
            //FLASH TEXT, HIGHLIGHT FIELDS IN ERROR
            eval(jsblock);
        }
        //UNEXPECTED ERROR, DON'T REDISPLAY THE FORM
        else
        {
            $("errorDisplaySpan").innerHTML = content.replace(/ERROR::/, "");
            ajaxInnerHtmlTarget.innerHTML = "";
            document.forms['report'].hidLoadingState.value = "complete";
            //IF THERE IS A REDIRECT TIMEOUT ERROR FIELD IN THE RETURNED HTML
            if ($("redirectTimeoutError"))
            {
                setTimeout
                (
                    "MenuFormSubmit('"
                    + $("redirectTimeoutError").value
                    + "','menuNavigation','logout.asp')"
                    ,5000
                );
            }
        }
    }
}

//PREVENTS ALERTS FROM DISPLAYING MORE THAN ONCE
var processFormAlertMessages = new Array();
function processFormAlert(alertMessage)
{
	alertFound = false;
    for(var i=0; i<processFormAlertMessages.length; i++) 
    {
	    if (processFormAlertMessages[i] == alertMessage)
	    {
	    	alertFound = true;
	    	break;
	    }
	}
	if (!alertFound)
	{
		alert(alertMessage);
		processFormAlertMessages.push(alertMessage);
	}
}

var FormDeltaCheckObserver;
var FormValidationObserver;
var bSubmitForm;
addLoadEvent(function()
{
    FormValidationObserver = new Observer;
    FormDeltaCheckObserver = new Observer;
});

function FormValidation(formName)
{
    bSubmitForm = true;
    processFormAlertMessages = new Array();
    FormDeltaCheckObserver.execute();
    if (deltaCheck(formName) == false)
    {
        bSubmitForm = false;
        alert("There are no modifications to process.");
    }
    else
    {
        FormValidationObserver.execute();
    }

    if (bSubmitForm == true)
    {
        ajaxProcessForm(formName);
    }
}

function enableFormElements(formId)
{
	for (i=0;i<$(formId).length;i++)
	{
		$(formId)[i].disabled = false;
	}
	captureFormValuesOnLoad(formId);
}

function confirmDelete(recordtype,form)
{
	//Up Two Levels to Account for ID and PageCode
    showPopWin(
        "../../ModalAction/"
        +"prompt=Are you sure you wish to delete this " + recordtype + "%3F"
        +"&buttonSet=DeleteConfirmation"
        +"&formName=" + form
        +"&formFieldToSet=deletefunction"
        +"&formFieldValue=true"
        ,300
        ,150
        ,''
        ,true
    );
}

//Dynamic Selectors and other JavaScript-Modified form elements
//don't have their onchange events triggered on reset, this function
//triggers them.
function resetForm()
{
  if (typeof refreshDynamicSelectors != 'undefined')
  {
      setTimeout("refreshDynamicSelectors()",100);
  }
}
