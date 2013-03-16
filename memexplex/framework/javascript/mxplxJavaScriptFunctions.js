// -----------------------------------------------------------------------
//   File Name: qmd_javascript.js
//     Created: 01/10/2001
//  Created By: Emil Moster, Ryan Somma
// Description: A collection of functions used on all EAL pages. Includes
//              functions to submit parameters with menu items,
//
//     History:
//
// -----------------------------------------------------------------------

//GRAB FORM ELEMENT PARAMETERS AND SUBMIT A FORM
function MenuFormSubmit(sAction,sParam1,sParamValue1,sParam2,sParamValue2,
    sParam3,sParamValue3,sParam4,sParamValue4,sParam5,sParamValue5)
{
    if (sAction != "")
    {
        var oForm = document.frmMenu
        oForm.action = sAction;
        if (sParam1 != "")
        {
            oForm.hidParam1.name = sParam1;
            oForm.hidParam1.value = sParamValue1;
            if (sParam2 != "")
            {
                oForm.hidParam2.name = sParam2;
                oForm.hidParam2.value = sParamValue2;
                if (sParam3 != "")
                {
                    oForm.hidParam3.name = sParam3;
                    oForm.hidParam3.value = sParamValue3;
                    if (sParam4 != "")
                    {
                        oForm.hidParam4.name = sParam4;
                        oForm.hidParam4.value = sParamValue4;
                        if (sParam5 != "")
                        {
                            oForm.hidParam5.name = sParam5;
                            oForm.hidParam5.value = sParamValue5;
                        }
                    }
                }
            }
            oForm.submit();
        }
    }
}

//LEADING ZERO
function LeadingZero(sValue)
{
    if (isNaN(sValue) || sValue < 0)
    {
        return "00";
    }
    else if (sValue < 10)
    {
        return "0" + sValue;
    }
    else
    {
        return sValue;
    }
}

// FUNCTION WILL FLASH TEXT FOR A VARIABLE AMOUNT OF TIME
function FlashText(sAnchor,sToggle,iSpeed,iCount)
{
    var blink_speed = iSpeed;
    if (iCount != 0)
    {
        if($(sAnchor) != null)
        {
            if(sToggle == "on")
            {
                $(sAnchor).style.visibility = "visible";
                sToggle = "off";
                iCount = (iCount - 1);
            }
            else
            {
                $(sAnchor).style.visibility = "hidden";
                sToggle = "on";
            }
            setTimeout("FlashText('" + sAnchor + "','" + sToggle + "'," + iSpeed + "," + iCount + ")",blink_speed);
        }
    }
}


//THESE FUNCTIONs ALLOW DIV TAGS TO LAYER OVERTOP SELECT FIELDS TO OVERCOME
//THE FACT THAT IE 6.x DOES NOT RENDER THE LAYERS PROPERLY
//PUTS AN IFRAME UNDER THE DIV TAG TO PUT IT ON TOP OF SELECT

function findPosX(obj)
{
	var curleft = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curleft += obj.offsetLeft
			obj = obj.offsetParent;
		}
	}
	else if (obj.x)
		curleft += obj.x;
	return curleft;
}

function findPosY(obj)
{
	var curtop = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curtop += obj.offsetTop
			obj = obj.offsetParent;
		}
	}
	else if (obj.y)
		curtop += obj.y;
	return curtop;
}

function positionIFrame(divid)
{
    var div = $(divid);
    var frm = $('divframe');
	if (parseFloat(navigator.appVersion.split("MSIE")[1]) < 7)
	{
		frm.style.left = findPosX(div);
		frm.style.top = findPosY(div);
	}
	else
	{
        frm.style.left = div.offsetLeft;
        frm.style.top = div.offsetTop;
	}
    frm.style.height = div.offsetHeight;
    frm.style.width = div.offsetWidth;
    frm.style.display = "block";
    frm.style.visibility = 'visible';
}

var closetimer	  = null;
var locktimer     = null;
var ddmenuitem    = null;
var menuunlocked  = false;

function unlockMenu(id)
{
	menuunlocked  = true;
	
    // cancel close timer
    cancelCloseDropdownTimeOut();
    
    // get new layer and show it
    ddmenuitem = $(id);
    ddmenuitem.style.visibility = 'visible';

    //PUT AN IFRAME UNDER THE DIV TO DISPLAY OVER SELECT FIELDS
    if (/*@cc_on!@*/false) //CHECK FOR IE
    {
    	positionIFrame(id);
    }
}

function lockMenu()
{
	menuunlocked = false;
}

// open hidden layer
function openDropdown(id)
{
	if (menuunlocked)
	{
	    // cancel close timer
	    cancelCloseDropdownTimeOut();
	
	    // close old layer
	    if(ddmenuitem) ddmenuitem.style.visibility = 'hidden';
	
	    // get new layer and show it
	    ddmenuitem = $(id);
	    ddmenuitem.style.visibility = 'visible';
	
	    //PUT AN IFRAME UNDER THE DIV TO DISPLAY OVER SELECT FIELDS
	    if (/*@cc_on!@*/false) //CHECK FOR IE
	    {
        	positionIFrame(id);
	    }
	}
}

// close showed layer
function closeDropdown()
{
    if(ddmenuitem) ddmenuitem.style.visibility = 'hidden';
    //HIDE THE IFRAME
    if (/*@cc_on!@*/false) //CHECK FOR IE
    {
        $('divframe').style.visibility = 'hidden';
    }
}

// go close timer
function closeDropdownTimeOut()
{
    closetimer = window.setTimeout(closeDropdown, 300);
    locktimer  = window.setTimeout(lockMenu, 2000);
}

// cancel close timer
function cancelCloseDropdownTimeOut()
{
    if(closetimer)
    {
        window.clearTimeout(closetimer);
        closetimer = null;
    }

    if(locktimer)
    {
	    window.clearTimeout(locktimer);
	    locktimer = null;
    }
}

// START SHOW/HIDE MENU ITEMS JAVASCRIPT
function showhide(id)
{
        obj = $(id);
        if (obj.style.display == "none")
        {
            // LOOP THROUGH AND HIDE ANY DISPLAYED DIVS
            var divs=document.getElementsByTagName('div');
            for (var i=0;i<divs.length;i++)
            {
                if (divs[i].id.indexOf('menu_') != -1)
                {
                    divs[i].style.display = "none";
                }
            }
            obj.style.display = "";
        } else {
            obj.style.display = "none";
        }

}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

////////////////////////////////////////////////////////////////
//
// BEGIN STANDARD AJAX METHOD FOR CALLING SERVER-SIDE FUNCTIONS
//
////////////////////////////////////////////////////////////////
var xget;
var callback;

function stateChangeHandler ()
{
    var str = "";

    if (xget.readyState == 4 || xget.readyState == 'complete')
    {

        str = xget.responseText;

        if (callback != '' && typeof callback != 'undefined')
        {
            if (str)
            {
                eval(callback + "(\"" + escape(str) + "\")");
            }
            else
            {
                eval(callback + "()");
            }
        }
    }
}

function getContent (url, params, callbackfunction)
{
    callback = callbackfunction;

    xget = getXMLHttp ();
    xget.open ("POST", url, true);

    //Send the proper header information along with the request
    xget.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    //SEND PARAMETERS
    xget.send (params);
}

function getXMLHttp ()
{
    var xmlHttp;
    // Internet Explorer
    try
    {
        xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
        xmlHttp.onreadystatechange = stateChangeHandler;
    }
    catch (e)
    {
        try
        {
            xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
            xmlHttp.onreadystatechange = stateChangeHandler;
        }
        catch (e)
        {
            try
            {
                // IE7/8, FireFox, Chrome etc.
                xmlHttp = new XMLHttpRequest;
                xmlHttp.onreadystatechange = stateChangeHandler;
            }
            catch (e)
            {
                alert("Your browser does not support AJAX!");
                return false;
            }
        }
    }
    return xmlHttp;
}
///////////////////////////////////////////////////////////////
//
// END STANDARD AJAX METHOD FOR CALLING SERVER-SIDE FUNCTIONS
//
///////////////////////////////////////////////////////////////

/*
 * JavaScript Observer Class designed by Dustin Diaz
 * http://www.dustindiaz.com/javascript-observer-class/
 *
 */

function Observer()
{
    this.fns = [];
}

Observer.prototype =
{
    subscribe : function(fn)
    {
        if (fn != undefined)
        {
            this.fns.push(fn);
        }
    },
    unsubscribe : function(fn)
    {
        var tmpfns = [];
        for ( var i=0, j=this.fns.length; i < j; ++i )
        {
            if ( this.fns[i] !== fn )
            {
                tmpfns.push(this.fns[i]);
            }
        }
        this.fns = tmpfns;
    },
    execute : function()
    {
        for ( var i=0, j=this.fns.length; i < j; ++i )
        {
            this.fns[i].call();
        }
    },
    unsubscribeall : function()
    {
    	this.fns = [];
    },
    //USED TO RESUBSCRIBE FORMELEMENTS AFTER
    //AN AJAX REFERESH, CHECKS TO ENSURE ELEMENT
    //STILL EXISTS BEFORE SUBSCRIBING IT.
    resubscribe : function(formFieldId, fn)
    {
        if (fn != undefined)
        {
            this.unsubscribe(fn);
            if ($(formFieldId) != undefined)
            {
                this.fns.push(fn);
            }
        }
    },
    //USED IN DEBUGING
    //CALL THIS TO MAKE SURE THE OBJECT HAS BEEN CREATED
    alert : function ()
    {
        alert("Working");
    }
};
/*
 * END
 * JavaScript Observer Classe designed by Dustin Diaz
 * http://www.dustindiaz.com/javascript-observer-class/
 *
 */
var ajaxInnerHtmlTarget = $('dataTable');
var loadingDisplaySpan = $('loadingDisplay');

function setAjaxContentTarget(ajaxTarget,loadingDisplay)
{
    ajaxInnerHtmlTarget = $(ajaxTarget);
    loadingDisplaySpan = $(loadingDisplay);
}

var ajaxGetHTMLObserver = new Observer;
function ajaxGetHTML(filter)
{
	ajaxGetHTMLObserver.execute();
	
    //$('dataTableAnchor').focus();
    document.report.hidLoadingState.value = "processing";
    s_timer = 0;

    loadingDisplay();

    application = document.forms['report'].elements['application'].value;
    pageCode = document.forms['report'].elements['pageCode'].value;

    sFilter = "";
    if (filter != undefined)
    {
        sFilter = filter;
    }
    else
    {
        //Grab all Filter Elements and Values and Submit in Query String
        if (typeof document.forms['reportFilter'] != 'undefined')
        {
            for(i=0;i<document.forms['reportFilter'].elements.length;i++)
            {
                if(document.forms['reportFilter'].elements[i].type == "text" || document.forms['reportFilter'].elements[i].type == "textarea" || document.forms['reportFilter'].elements[i].type == "hidden")
                {
                    sFilter = sFilter + "&" + document.forms['reportFilter'].elements[i].id + "=" + document.forms['reportFilter'].elements[i].value;
                }
                else if(document.forms['reportFilter'].elements[i].type == "checkbox")
                {
                    sFilter = sFilter + "&" + document.forms['reportFilter'].elements[i].id + "=" + document.forms['reportFilter'].elements[i].checked;
                }
                else if(document.forms['reportFilter'].elements[i].type == "select-one")
                {
                    //CHECK TO MAKE SURE THERE ARE OPTIONS IN THE SELECT
                    if (document.forms['reportFilter'].elements[i].options[0] != null)
                    {
                        sFilter = sFilter + "&" + document.forms['reportFilter'].elements[i].id + "=" + document.forms['reportFilter'].elements[i].options[document.forms['reportFilter'].elements[i].options.selectedIndex].value;
                    }
                }
            }
        }
    }

    //Grab all Variables from the Querystring ("GET" variables,
    //since they are not transfered through the AJAX call automatically)
    //http://www.tek-tips.com/faqs.cfm?fid=5442
    var qparts = window.location.href.split("/");
    if (qparts[qparts.length-1].indexOf('=') != -1)
    {
        sFilter = sFilter + "&" + qparts[qparts.length-1];
    }

    //KLUGE ALERT!!! - This is MemexPlex-Specific, not a Framework thing.
    //Get Object ID from URL, if Present.
    var idIndex = qparts.length-2;
    if ((parseFloat(qparts[idIndex]) == parseInt(qparts[idIndex])) && !isNaN(qparts[idIndex]))
    {
    	sFilter = sFilter + "&id=" + qparts[idIndex];
    }

    //This will call the dynamic web page and pass along my data.
    //It is a PHP page, which will run server-side.  The second
    //parameter indicates what JavaScript function should trigger
    //when the callback is done
    getContent(applicationRootFolder + "framework/api/report.php", "application=" + application + "&pageCode=" + pageCode + sFilter, 'fillInTableCallback');
}

function assembleJavaScriptFromString(content)
{
    var jsblock = "";
    while(content.match(/(<script[^>]+javascript[^>]+>\s*(<!--)?)/i))
    {
    	content = content.substr(content.indexOf(RegExp.$1) + RegExp.$1.length);
        if (!content.match(/((-->)?\s*<\/script>)/)) break;
        block = content.substr(0, content.indexOf(RegExp.$1));
        jsblock = jsblock + block;
        content = content.substring(block.length + RegExp.$1.length);
    }
    return jsblock;
}

//Observer object for executing other scripts based on
//ajax call completion (ie. dynamic menus or header information)
//See /javascript/trainingAndQualificationsDynamicMenu.js for example.
var fillInTableCallbackObserver = new Observer;
var fillInTableCallbackJavaScriptLoadedObserver = new Observer;
function fillInTableCallback(resultContent)
{
    //This is called when the Callback is done!
    //So the callback gets the data and now we say what to do with it.
	var content = unescape(resultContent);
    document.report.hidLoadingState.value = "complete";
    ajaxInnerHtmlTarget.innerHTML = content;
    
    //Notify Observers
    fillInTableCallbackObserver.execute();

    //RESET JAVASCRIPT BASED ON RETURNED SCRIPTS
    var jsblock = assembleJavaScriptFromString(content);
    
    if (jsblock != "")
    {
        window.onload = null;
	    var oScript = document.createElement('script');
	    oScript.setAttribute('type','text/javascript');
	    oScript.text = jsblock;
	    document.getElementsByTagName("head").item(0).appendChild(oScript);
	    //RESET WINDOW.ONLOAD FUNCTIONS
	    if (typeof window.onload == 'function') 
	    {
	    	window.onload();
	    }
    }

    //Notify Observers
    fillInTableCallbackJavaScriptLoadedObserver.execute();

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

var s_timer = 0;
function loadingDisplay()
{
    if (document.report.hidLoadingState.value == "processing")
    {
        

        s_timer = s_timer + 1;

        ajaxInnerHtmlTarget.style.display = "none";
        loadingDisplaySpan.style.display = "";
        //loadingDisplay.innerHTML = "<br /><center><span CLASS=\"largeBlue\"><b><img src=\"" + applicationRootFolder + "framework/images/loading.gif\" align=\"absmiddle\">&nbsp;Loading</span></center>";

        if (s_timer > 120)
        {
            loadingDisplaySpan.innerHTML = "<p><table border=\"0\" align=\"center\"><tr><td><span class=\"largeBlue\">Request Timed Out</span></td></tr></table>";
        }
        else if (document.report.hidLoadingState.value == "processing")
        {
            setTimeout("loadingDisplay()",1000);
        }
    }
    else
    {
        ajaxInnerHtmlTarget.style.display = "";
        loadingDisplaySpan.style.display = "none";
    }
}

//window.onload = function()
//addLoadEvent(function()
//{
//    ajaxGetHTML();
//});
/////////////////////////////////////////////////////
//
// End AJAX GetHTML Methods
//
/////////////////////////////////////////////////////

// CLOSE ALL POP-UP WINDOWS
var newWin = null;
var child = null;
var childTwo = null;
var childThree = null;
var childFour = null;

function closeWindows()
{
    if (child != null)
    {
        child.close();
    }
    if (childTwo != null)
    {
        childTwo.close();
    }
    if (childThree != null)
    {
        childThree.close();
    }
    if (childFour != null)
    {
        childFour.close();
    }
    if (newWin != null)
    {
        newWin.close();
    }
}

// SET DYNAMIC PORTION OF THE HEADER

function setHeader(text)
{
    $('dynamicHeader').innerHTML = '&nbsp;[' + text + ']';
}

/**
 * Gets the current query string, appends existing parameters,
 * gets value for submitted parameter and redirects.
 * @param paramKey
 */
function assembleQueryStringAndSubmit(paramKey,paramValue)
{
	var baseUrl = document.URL.substring(0,(document.URL.lastIndexOf('/')+1));
	var query = document.URL.substring((document.URL.lastIndexOf('/')+1),document.URL.length);
	var parms = query.split('&');
	urlString = '';
	amp = '';
	for (var i=0; i<parms.length; i++) 
	{
		appendString = true;
		var pos = parms[i].indexOf('=');
		if (pos > 0) 
		{
			var key = parms[i].substring(0,pos);
			//var val = parms[i].substring(pos+1);
			//qsParm[key] = val;
			if (key == paramKey || key == 'page' || key == 'successMessage')
		    {
				appendString = false;
		    }
		}
		
		if (appendString)
	    {
			urlString = urlString + amp + parms[i];
			if (urlString != '')
			{
				amp = '&';
			}
	    }
	}
	
    if(paramValue == null)
    {
    	paramValue = $(paramKey).value;
    }
	urlString = urlString + amp + paramKey + '=' + encodeURI(paramValue.replace(/&/g, '%26'))
	window.location = baseUrl + urlString;
}

/**
 * X-browser event handler attachment and detachment
 * TH: Switched first true to false per http://www.onlinetools.org/articles/unobtrusivejavascript/chapter4.html
 *
 * @argument obj - the object to attach event to
 * @argument evType - name of the event - DONT ADD "on", pass only "mouseover", etc
 * @argument fn - function to call
 */
function addEvent(obj, evType, fn)
{
    if (obj.addEventListener)
    {
        obj.addEventListener(evType, fn, true);
        return true;
    }
    else if (obj.attachEvent)
    {
        var r = obj.attachEvent("on"+evType, fn);
        return r;
    }
    else
    {
        return false;
    }
}

function removeEvent(obj, evType, fn, useCapture){
  if (obj.removeEventListener){
    obj.removeEventListener(evType, fn, useCapture);
    return true;
  } else if (obj.detachEvent){
    var r = obj.detachEvent("on"+evType, fn);
    return r;
  } else {
    alert("Handler could not be removed");
  }
}


