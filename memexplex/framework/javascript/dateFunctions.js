// -----------------------------------------------------------------------
//   File Name: dateFunctions.js
//     Created: 01/10/2001
//  Created By: Emil Moster / Ryan Somma
// Description: Include this file for any page where a date input box is
//              present. Provided here are a pop-up calender, a date-roller,
//              and date validation.
//
//     History: 03/01/2009 RAS Modified for cross-browser compatibility.
//
// -----------------------------------------------------------------------

var sDefaultDate;

// COLOR & STYLE DEFINITIONS
topBackground         = "#990000";
topMarginTop          = "0px";
topMarginLeft         = "0px";
topMarginRight        = "0px";
topMarginBottom       = "0px";

bottomBackground      = "#990000";
bottomMarginTop       = "4px";
bottomMarginLeft      = "0px";
bottomMarginRight     = "0px";
bottomMarginBottom    = "0px";


headingCellColor      = "#000066";
headingTextColor      = "#FFFFFF";

tableBGColor          = "#000000";
tdColor               = "#FFFFFF";
tdFocusColor          = "#990000";

focusLinkColor        = "#FFFFFF";
focusLinkHoverColor   = "#FFFFFF";
focusLinkVisitedColor = "#FFFFFF";

linkColor             = "#000066";
linkHoverColor        = "#32CD32";
linkVisitedColor      = "#000066";

fontStyle             = "8pt arial";
headingFontStyle      = "bold 8pt arial";
bottomBorder          = false;
tableBorder           = 0;

FormReturn            = "";

// DETERMINE BROWSER
var aSelects;

selectedLanguage = navigator.language;

function openCalendar(applicationDirectory)
{
    top.newWin = window.open
    (
        applicationDirectory + 'calendar.html'
        ,'cal'
        ,'width=170,height=193,left=50,top=50,status=no,toolbar=no,location=no,menubar=no,titlebar=no'
    );
}

function setDateField(dateField,oForm)
{
    calDateField = dateField;
    inDate = dateField.value;
    if (oForm != null)
    {
        FormReturn = oForm;
    }
    setInitialDate();
    calDocTop    = buildTopCalFrame();
    calDocBottom = buildBottomCalFrame();
}

function setDefaultDate(defaultDate)
{
    sDefaultDate = new Date(defaultDate);
}

function setInitialDate()
{
    calDate = new Date(inDate);
    if (isNaN(calDate))
    {
        //sDefaultDate is set on the eal_header.asp page.
        if (sDefaultDate != "")
        {
            calDate = new Date(sDefaultDate);
        }
        else
        {
            calDate = new Date();
        }
    }
    calDay  = calDate.getDate();
    calDate.setDate(1);
}

function showCalendar(dateField)
{
    setDateField(dateField);
    top.newWin = window.open("javascript:parent.opener.calDocFrameset", "calWin", winPrefs);
    top.newWin.focus();
}

function buildTopCalFrame()
{
    var calDoc =
    "<html>" +
    "<head>" +
    "<style type=\"text/css\">" +
    "<!--" +
    "a { text-decoration: none; }" +

    "a:visited { color: #FFFFFF; font-family: arial; font-weight: bold; }" +
    "a:link { color:  #FFFFFF; font-family: arial; font-weight: bold; }" +
    "a:hover { color:  #FFFFFF; font-family: arial; font-weight: bold; }" +

    "-->" +
    "</style>" +
    "<script type=\"text/javascript\">" +
    "function windowfocus()" +
    "{" +
    "window.focus();" +
    "}" +
    "</scrip" + "t>" + //Causes string error when one word. http://forums.devx.com/showthread.php?t=11738
    "</head>" +
    "<body onload=\"windowfocus();\" bgcolor=\"" + topBackground + "\" topmargin=\"" + topMarginTop + "\" bottommargin=\"" + topMarginBottom + "\" leftmargin=\"" + topMarginLeft + "\" rightmargin=\"" + topMarginTop + "\">" +
    "<form name=\"frmCalendar\" onsubmit=\"return false;\">" +
    "<center>" +
    "<table cellpadding=\"0\" cellspacing=\"1\" border=\"0\">" +
    "<tr><td colspan=\"7\">" +
    "<center>" +
    getMonthSelect() +
    "<input name=\"year\" value=\"" + calDate.getFullYear() + "\" type=\"text\" size=\"4\" maxlength=\"4\" onchange=\"parent.opener.setYear()\" readonly>" +
    "</center>" +
    "</td>" +
    "</tr>" +
    "<tr>" +
    "<td colspan=\"7\">" +
    "<center>" +
    "<a href=\"javascript:parent.opener.setPreviousYear()\" alt=\"Previous Year\"><<</a>&nbsp;&nbsp;" +
    "<a href=\"javascript:parent.opener.setPreviousMonth()\"><</a>&nbsp;&nbsp;" +
    "<a href=\"javascript:parent.opener.setToday()\">Today</a>&nbsp;&nbsp;" +
    "<a href=\"javascript:parent.opener.setNextMonth()\">></a>&nbsp;&nbsp;" +
    "<a href=\"javascript:parent.opener.setNextYear()\">>></a>" +
    "</center>" +
//    "<input type=\"button\" name=\"previousYear\" value=\"<<\" onclick=\"parent.opener.setPreviousYear()\">" +
//    "<input type=\"button\" name=\"previousMonth\" value=\" < \" onclick=\"parent.opener.setPreviousMonth()\">" +
//    "<input type=\"button\" name=\"today\" value=\"Today\" onclick=\"parent.opener.setToday()\">" +
//    "<input type=\"button\" name=\"nextMonth\" value=\" > \" onclick=\"parent.opener.setNextMonth()\">" +
//    "<input type=\"button\" name=\"nextYear\" value=\">>\"  onclick=\"parent.opener.setNextYear()\">" +
    "</td>" +
    "</tr>" +
    "</table>" +
    "</center>" +
    "</form>" +
    "</body>" +
    "</html>";

    return calDoc;
}


function buildBottomCalFrame()
{
    var calDoc  = calendarBegin;
    month       = calDate.getMonth();
    year        = calDate.getFullYear();
    day         = calDay;
    var i       = 0;
    var days    = getDaysInMonth();
    if (day > days)
    {
        day = days;
    }
    var firstOfMonth = new Date (year, month, 1);
    var startingPos  = firstOfMonth.getDay();
    days += startingPos;
    var columnCount = 0;
    for (i = 0; i < startingPos; i++)
    {
        calDoc += blankCell;
        columnCount++;
    }
    var currentDay = 0;
    var tdType     = "BGColor";
    var dayType    = "weekday";
    for (i = startingPos; i < days; i++)
    {
        var paddingChar = "&nbsp;";
        if (i-startingPos+1 < 10)
        {
            padding = "&nbsp;&nbsp;";
        }
        else
        {
            padding = "&nbsp;";
        }
        currentDay = i-startingPos+1;
        if (currentDay == day)
        {
            tdType  = "focusbgcolor";
            dayType = "focusday";
        }
        else
        {
            tdType  = "bgcolor";
            dayType = "weekday";
        }
        calDoc += "<td class='" + tdType + "'>" +
        "<a class='" + dayType + "' href='javascript:parent.opener.returnDate(" +
        currentDay + ")'>" + padding + currentDay + paddingChar + "</a></td>";
        columnCount++;
        if (columnCount % 7 == 0)
        {
            calDoc += "</tr><tr>";
        }
    }
    for (i=days; i<42; i++)
    {
        calDoc += blankCell;
        columnCount++;
        if (columnCount % 7 == 0)
        {
            calDoc += "</tr>";
            if (i<41)
            {
                calDoc += "<tr>";
            }
        }
    }
    calDoc += calendarEnd;
    return calDoc;
}

function writeCalendar()
{
    calDocBottom = buildBottomCalFrame();
    top.newWin.frames['bottomCalFrame'].document.open();
    top.newWin.frames['bottomCalFrame'].document.write(calDocBottom);
    top.newWin.frames['bottomCalFrame'].document.close();
}

function setToday()
{
    //sDefaultDate set on the eal_header page.
    if (sDefaultDate != "")
    {
        calDate = new Date(sDefaultDate);
    }
    else
    {
        calDate = new Date();
    }

    var day           = calDate.getDate();
    var month         = calDate.getMonth()+1;
    var year          = calDate.getFullYear();

    day = makeTwoDigit(day);
    month = makeTwoDigit(month);

    outDate = (month + "/" + day + "/" + year);

    calDateField.value = outDate;
    calDateField.focus();
    if (FormReturn != "")
    {
        FormReturn.submit();
        top.newWin.close();
    }
    else
    {
        top.newWin.close();
    }
}

function setYear()
{
    var year  = top.newWin.frames['topCalFrame'].document.frmCalendar.year.value;
    if (isFourDigitYear(year))
    {
        calDate.setFullYear(year);
        writeCalendar();
    }
    else
    {
        top.newWin.frames['topCalFrame'].document.frmCalendar.year.focus();
        top.newWin.frames['topCalFrame'].document.frmCalendar.year.select();
    }
}

function setCurrentMonth()
{
    var month = top.newWin.frames['topCalFrame'].document.frmCalendar.month.selectedIndex;
    calDate.setMonth(month);
    writeCalendar();
}

function setPreviousYear()
{
    var year  = top.newWin.frames['topCalFrame'].document.frmCalendar.year.value;
    if (isFourDigitYear(year) && year > 1000)
    {
        year--;
        calDate.setFullYear(year);
        top.newWin.frames['topCalFrame'].document.frmCalendar.year.value = year;
        writeCalendar();
    }
}

function setPreviousMonth()
{
    var year  = top.newWin.frames['topCalFrame'].document.frmCalendar.year.value;
    if (isFourDigitYear(year))
    {
        var month = top.newWin.frames['topCalFrame'].document.frmCalendar.month.selectedIndex;
        if (month == 0)
        {
            month = 11;
            if (year > 1000)
            {
                year--;
                calDate.setFullYear(year);
                top.newWin.frames['topCalFrame'].document.frmCalendar.year.value = year;
            }
        }
        else
        {
            month--;
        }
        calDate.setMonth(month);
        top.newWin.frames['topCalFrame'].document.frmCalendar.month.selectedIndex = month;
        writeCalendar();
    }
}

function setNextMonth()
{
    var year = top.newWin.frames['topCalFrame'].document.frmCalendar.year.value;
    if (isFourDigitYear(year))
    {
        var month =  top.newWin.frames['topCalFrame'].document.frmCalendar.month.selectedIndex;
        if (month == 11)
        {
            month = 0;
            year++;
            calDate.setFullYear(year);
            top.newWin.frames['topCalFrame'].document.frmCalendar.year.value = year;
        }
        else
        {
            month++;
        }
        calDate.setMonth(month);
        top.newWin.frames['topCalFrame'].document.frmCalendar.month.selectedIndex = month;
        writeCalendar();
    }
}

function setNextYear()
{
    var year  = top.newWin.frames['topCalFrame'].document.frmCalendar.year.value;
    //var year  = top.newWin.frames('topCalFrame').document.frmCalendar.year.value;
    //var year  = frames('topCalFrame').document.frmCalendar.year.value;
    if (isFourDigitYear(year))
    {
        year++;
        calDate.setFullYear(year);
        top.newWin.frames['topCalFrame'].document.frmCalendar.year.value = year;
        writeCalendar();
    }
}

function getDaysInMonth()
{
    var days;
    var month = calDate.getMonth()+1;
    var year  = calDate.getFullYear();
    if (month==1 || month==3 || month==5 || month==7 || month==8 ||
        month==10 || month==12)
        {
        days=31;
    }
    else if (month==4 || month==6 || month==9 || month==11)
    {
        days=30;
    }
    else if (month==2)
    {
        if (isLeapYear(year))
        {
            days=29;
        }
        else
        {
            days=28;
        }
    }
    return (days);
}

function isLeapYear(Year)
{
    if (((Year % 4)==0) && ((Year % 100)!=0) || ((Year % 400)==0))
    {
        return (true);
    }
    else
    {
        return (false);
    }
}

function isFourDigitYear(year)
{
    if (year.length != 4)
    {
        top.newWin.frames['topCalFrame'].document.frmCalendar.year.value = calDate.getFullYear();
        top.newWin.frames['topCalFrame'].document.frmCalendar.year.select();
        top.newWin.frames['topCalFrame'].document.frmCalendar.year.focus();
        return true;
    }
    else
    {
        return true;
    }
}

function getMonthSelect()
{
    monthArray = new Array('January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December');
    var activeMonth = calDate.getMonth();
    monthSelect = "<select name='month' onChange='parent.opener.setCurrentMonth()'>";
    for (i in monthArray)
    {
        if (i == activeMonth)
        {
            monthSelect += "<option selected>" + monthArray[i];
        }
        else
        {
            monthSelect += "<option>" + monthArray[i];
        }
    }
    monthSelect += "</select>";
    return monthSelect;
}

function createWeekdayList()
{
    weekdayList  = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    weekdayArray = new Array('Su','Mo','Tu','We','Th','Fr','Sa');
    var weekdays = "<tr bgcolor=\"" + headingCellColor + "\">";
    for (i in weekdayArray)
    {
        weekdays += "<td class=\"heading\">" + weekdayArray[i] + "</td>";
    }
    weekdays += "</tr>";
    return weekdays;
}

function buildCalParts()
{
    weekdays = createWeekdayList();
    blankCell = "<td bgcolor=\"" + tdColor + "\">&nbsp;&nbsp;&nbsp;</td>";
    calendarBegin =
    "<html>" +
    "<head>" +

    "<style type=\"text/css\">" +
    "<!--" +
    "td.heading { text-decoration: none; color:" + headingTextColor + "; font: " + headingFontStyle + "; }" +
    "td.focusbgcolor { background-color:" + tdFocusColor + "; }" +
    "td.bgcolor { background-color:" + tdColor + "; }" +

    "a { text-decoration: none; }" +

    "a.weekday:visited { color: " + linkColor + "; font: " + fontStyle + "; }" +
    "a.weekday:link { color: " + linkColor + "; font: " + fontStyle + "; }" +
    "a.weekday:hover { color: " + linkHoverColor + "; font: " + fontStyle + "; }" +

    "a.focusday:visited { color: " + focusLinkColor + "; font: " + fontStyle + "; }" +
    "a.focusday:link { color: " + focusLinkColor + "; font: " + fontStyle + "; }" +
    "a.focusday:hover { color: " + focusLinkColor + "; font: " + fontStyle + "; }" +

    "-->" +
    "</style>" +

    "</head>" +
    "<body bgcolor=\"" + bottomBackground + "\" TopMargin=\"" + bottomMarginTop + "\" BottomMargin=\"" + bottomMarginBottom + "\" LeftMargin=\"" + bottomMarginLeft + "\" RightMargin=\"" + bottomMarginTop + "\">" +
    "<center>";
    calendarBegin +=
    "<table cellpadding=\"0\" cellspacing=\"1\" border=\"" + tableBorder + "\" bgcolor=\"" + tableBGColor + "\">" +
    weekdays +
    "<tr>";
    calendarEnd = "";
    if (bottomBorder)
    {
        calendarEnd += "<tr></tr>";
    }
    calendarEnd +=
    "</table>" +
"</center>" +
"</body>" +
"</html>";
}

//BUILDING THE CALENDAR
buildCalParts();

function jsReplace(inString, find, replace)
{
    if (!inString)
    {
        return "";
    }
    if (inString.indexOf(find) != -1)
    {
        t = inString.split(find);
        return (t.join(replace));
    }
    else
    {
        return inString;
    }
}

function doNothing()
{
}

function makeTwoDigit(inValue)
{
    var numVal = parseInt(inValue, 10);
    if (numVal < 10)
    {
        return("0" + numVal);
    }
    else
    {
        return numVal;
    }
}

function returnDate(inDay)
{
    calDate.setDate(inDay);
    var day           = calDate.getDate();
    var month         = calDate.getMonth()+1;
    var year          = calDate.getFullYear();

    day = makeTwoDigit(day);
    month = makeTwoDigit(month);

    outDate = (month + "/" + day + "/" + year);

    calDateField.value = outDate;
    calDateField.focus();
    if (FormReturn != "")
    {
        FormReturn.submit();
        top.newWin.close();
    }
    else
    {
        top.newWin.close();
    }
}

//NAVIGATING FORWARD AND BACKWARD WITH CALENDAR
function ModifySubmitDate(change)
{
    $("hidModifySubmitDate").value = change;
}

function CheckDate(oField,iLoop,bExceptBlanks)
{
    var bCompleteVerificationCheck = true;

    for (iCheckDateLoop = 0 ; iCheckDateLoop < iLoop ; iCheckDateLoop++)
    {
        if ((iLoop != 1) && ($(oField + iCheckDateLoop).value != "") || (iLoop == 1) && ($(oField).value != ""))
        {
            var sField;
            var sFieldValue;
            var sFieldValueTemp;
            var aDateArray = "";
            var iDay;
            var iMonth;
            var iYear;
            var iLastDay;
            var iYearLen;
            var aSeparatorArray = new Array("/");
            var iValueChar;
            var bValidDate = true;

            if (iLoop != 1)
            {
                sField      = (oField + iCheckDateLoop)
                sFieldValue = $(oField + iCheckDateLoop).value;
            }
            else
            {
                sField      = (oField)
                sFieldValue = $(oField).value;
            }

            sFieldValueTemp = "";
            filteredValues = " ";
            for (iValueLoop = 0; iValueLoop < sFieldValue.length; iValueLoop++)
            {
                sCurrentChar = sFieldValue.charAt(iValueLoop);
                if (filteredValues.indexOf(sCurrentChar) == -1)
                {
                    sFieldValueTemp += sCurrentChar;
                }
            }

            if (sFieldValueTemp != "")
            {
                sFieldValue = sFieldValueTemp;
            }

            filteredValues = "1234567890/";
            for (iValueLoop = 0; iValueLoop < sFieldValue.length; iValueLoop++)
            {
                var sCurrentChar = sFieldValue.charAt(iValueLoop);
                if (filteredValues.indexOf(sCurrentChar) == -1)
                {
                    bValidDate = false;
                }
            }

            for (iValueChar = 0; iValueChar < aSeparatorArray.length; iValueChar++)
            {
                if (sFieldValue.indexOf(aSeparatorArray[iValueChar]) != -1)
                {
                    aDateArray = sFieldValue.split(aSeparatorArray[iValueChar]);
                    if (aDateArray.length != 3)
                    {
                        bValidDate = false;
                    }
                    else
                    {
                        iMonth    = LeadingZero(parseFloat(aDateArray[0]));
                        iDay      = LeadingZero(parseFloat(aDateArray[1]));
                        iYear     = aDateArray[2];
                        iYearLen  = aDateArray[2].length;
                    }
                }
            }
            if (aDateArray == "")
            {
                bValidDate = false;
            }

            if (iMonth == "01" || iMonth == "03" || iMonth == "05" || iMonth == "07" || iMonth == "08" || iMonth == "10" || iMonth == "12")
            {
                iLastDay = 31;
            }
            else if (iMonth == "04" || iMonth == "06" || iMonth == "09" || iMonth == "11")
            {
                iLastDay = 30;
            }
            else if (iMonth == 02)
            {
                if (LeapYear(iYear))
                {
                    iLastDay = 29;
                }
                else
                {
                    iLastDay = 28;
                }
            }
            else
            {
                bValidDate = false;
            }
            if ((parseFloat(iDay) > parseFloat(iLastDay)) || (parseFloat(iDay) < 1))
            {
                bValidDate = false;
            }
            if (iYearLen == 2)
            {
                if (iYear < 40)
                {
                    iYear = (2000 + parseFloat(iYear));
                }
                else
                {
                    iYear = (1900 + parseFloat(iYear));
                }
            }
            else if (iYearLen != 4)
            {
                bValidDate = false;
            }
            if (bValidDate == true)
            {
                $(sField).value = iMonth + "/" + iDay + "/" + iYear;
            }
            else if (bValidDate == false)
            {
                $(sField).value = "";
                $(sField).focus();
                bCompleteVerificationCheck = false;
            }
        }
        else if (bExceptBlanks == false)
        {
            return false;
        }
    }
    if (bCompleteVerificationCheck == true)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function LeapYear(intYear)
{
    if (intYear % 100 == 0)
    {
        if (intYear % 400 == 0)
        {
            return true;
        }
    }
    else
    {
        if ((intYear % 4) == 0)
        {
            return true;
        }
    }
    return false;
}

// CONFIRM SUBMIT OF A FORM
function ConfirmSubmit(oForm,msg,FormField)
{
    var ConfirmSubmit = confirm(msg);
    if (FormField == "")
    {
        if (ConfirmSubmit == true)
        {
            oForm.submit();
        }
    }
    else if (FormField != "")
    {
        if (ConfirmSubmit == true)
        {
            $(FormField).value = "Y";
        }else{
            $(FormField).value = "N";
        }
        oForm.submit();
    }
}

//DATE ROLLER + AND - ON NUMBER PAD TO CHANGE A DATE
var dDate;
function DateRoller(e,oSender,defaultDate)
{
    //distinguish between IE's explicit event object (window.event) and Firefox's implicit.
    var evtobj = window.event? event : e
    sDefaultDate = defaultDate;
    if ((evtobj.keyCode == 107 || evtobj.keyCode == 109) && (oSender.value.length < 10) || (evtobj.keyCode == 84))
    {
        dDate = new Date();
        if (sDefaultDate != "")
        {
            dDate = new Date(sDefaultDate);
        }
        oSender.value = LeadingZero((dDate.getMonth() + 1)) + "/" + LeadingZero(dDate.getDate()) + "/" + dDate.getFullYear();
    }
    else if (evtobj.keyCode == 107 || evtobj.keyCode == 109)
    {
        var sValue = oSender.value
        dDate = new Date(sValue);

        if (isNaN(dDate))
        {
            dDate = new Date();
            if (sDefaultDate != "")
            {
                dDate = new Date(sDefaultDate);
            }
            oSender.value = LeadingZero(dDate.getMonth() + 1) + "/" + LeadingZero(dDate.getDate()) + "/" + dDate.getFullYear();
        }
        else if (evtobj.keyCode == 107)
        {
            dDate = new Date(dDate.getFullYear(),dDate.getMonth(),(dDate.getDate() + 1));
            oSender.value = LeadingZero((dDate.getMonth() + 1)) + "/" + LeadingZero(dDate.getDate()) + "/" + dDate.getFullYear();
        }
        else
        {
            dDate = new Date(dDate.getFullYear(),dDate.getMonth(),(dDate.getDate() - 1));
            oSender.value = LeadingZero((dDate.getMonth() + 1)) + "/" + LeadingZero(dDate.getDate()) + "/" + dDate.getFullYear();
        }
    }
}

// FUTURE DATE CHECK
function futureDateCheck(fieldToCheck, presentDate)
{
    if ($(fieldToCheck).value != '')
    {
        dt1 = new Date($(fieldToCheck).value);
        dt2 = new Date(presentDate);

        if(dt1 > dt2)
        {
            return false;
        }
    }
    return true;
}

