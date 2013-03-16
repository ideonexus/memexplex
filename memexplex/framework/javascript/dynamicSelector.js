// -----------------------------------------------------------------------
//   File Name: dynamicSelector.js
//     Created: 10/29/2008
//  Created By: Ryan Somma
// Description: These javascript functions set dynamic selectors, and have
//              one selector populate another based on CLASS attributes. 
//
// Ganked from http://www.bobbyvandersluis.com/articles/unobtrusive_dynamic_select/index.html
// Updated by Ryan somma to preserver existing onchange functions
//
//     History: 20080302 RAS Modified so the function may be instantiated as an
//                           object so multiple selectors on a page can use it.
//
// -----------------------------------------------------------------------

// Ganked from http://www.bobbyvandersluis.com/articles/unobtrusive_dynamic_select/index.html
// Tweeked by Ryan Somma to preserve existing onchange events.
// These functions cannot handle executing dynamicSelect on the same field twice
// don't know how to overcome this, has something to do with the objects being
// preserved. In the Meantime, add optional elements if you want one selector
// to modify multiple options.

//http://www.webdeveloper.com/forum/archive/index.php/t-167010.html
//http://www.google.com/search?hl=en&q=adding+multiple+onchange+functions&aq=f&oq=
//http://www.evolution-internet.com/109/category/unobtrusive-dynamic-drop-down-boxes.aspx

var dynamicSelectArray = [];

// BECAUSE AJAX CALLS REFRESH FORM ELEMENTS WITHOUT RELOADING THE PAGE
// OBJECT-TO-FORM ELEMENT CONNECTIONS ARE LOST WHEN THE CONTENT REFRESHES
// THE FOLLOWING FUNCTION WILL CLEAR ALL DYNAMIC SELECTORS ALLOWING THEM
// TO BE RELOADED WHEN XHR COMPLETES.
function clearAllDynamicSelectors()
{
    for (dynamicSelectArrayCount = 0; dynamicSelectArrayCount < dynamicSelectArray.length; ++dynamicSelectArrayCount)
    {
        dynamicSelectArray[dynamicSelectArrayCount] = null;
    }
	dynamicSelectArray = [];
}

// THE FOLLOWING FUNCTION WILL RECONNECT OBJECTS TO THEIR FORM ELEMENTS
function reinitializeDynamicSelectors()
{
    for (dynamicSelectArrayCount = 0; dynamicSelectArrayCount < dynamicSelectArray.length; ++dynamicSelectArrayCount)
    {
        dynamicSelectArray[dynamicSelectArrayCount].reinitialize();
    }
}

//REFRESHES ALL DYNAMIC SELECTORS, NEEDED FOR FORM RESETS
function refreshDynamicSelectors()
{
    for (dynamicSelectArrayCount = 0; dynamicSelectArrayCount < dynamicSelectArray.length; ++dynamicSelectArrayCount)
    {
        dynamicSelectArray[dynamicSelectArrayCount].refreshOptions();
    }
}

function DynamicSelector() {}

DynamicSelector.prototype =
{
    initialize : function(parent, preserveSelectedIndex, useParentIdNotValue)
    {
        // Browser and feature tests to see if there is enough W3C DOM support
        agt = navigator.userAgent.toLowerCase();
        is_ie = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));
        is_mac = (agt.indexOf("mac") != -1);
        if (!(is_ie && is_mac) && document.getElementById && document.getElementsByTagName)
        {
            //Initialize Arrays
            this.fns = [];
            this.childSelectBoxObjectArray = [];
            this.childSelectBoxObjectOptionValues = [];
            this.childSelectBoxObjectOptionText = [];
            this.childSelectBoxObjectOptionClass = [];
            this.childSelectBoxObjectOptionId = [];

            // Obtain references to parent select boxes
            this.parentSelectBox = $(parent);
//alert("initialize " + this.parentSelectBox.id);

            // Obtain preserveSelectedIndexIndicator
            if (preserveSelectedIndex == 'true')
            {
                this.preserveSelectedIndexIndicator = true;
            }
            else
            {
                this.preserveSelectedIndexIndicator = false;
            }

            // Obtain useParentIdNotValue
            // This will have the child selector compare against the id attribute
            // of the option vice its value in case there is an indirect relationship
            if (useParentIdNotValue == 'true')
            {
                this.useParentIdNotValue = true;
            }
            else
            {
                this.useParentIdNotValue = false;
            }

            // Obtain references to parent select boxes
            this.parentSelectBox = $(parent);

            if (this.parentSelectBox != undefined)
            {
                // Onchange of the main select box: call a generic function to
                // display the related options in the dynamic select box
                var thisObj = this; //CLOSURE
                addEvent(this.parentSelectBox,'change',function() { thisObj.refreshOptions(); });

                //ADD OBJECT TO ARRAY OF DYNAMIC SELECT OBJECTS
                dynamicSelectArray.push(thisObj);
            }
            else
            {
                alert("DynamicSelector error: " + parent + " does not exist.");
            }
        }
    },
    reinitialize : function()
    {
        if ($(this.parentSelectBox.id) != undefined)
        {
            // Obtain references to parent select boxes
            this.parentSelectBox = $(this.parentSelectBox.id);

            // Onchange of the main select box: call a generic function to
            // display the related options in the dynamic select box
            var thisObj = this; //CLOSURE
            addEvent(this.parentSelectBox,'change',function() { thisObj.refreshOptions(); });

            // Clear childSelectBoxObjectArray
            childSelectBoxObjectTempArray = this.childSelectBoxObjectArray.slice();
            this.childSelectBoxObjectArray.length = 0;

            //Loop through childSelectBoxObjects
            for (childSelectBoxObjectArrayCount = 0; childSelectBoxObjectArrayCount < childSelectBoxObjectTempArray.length; ++childSelectBoxObjectArrayCount)
            {
                if ($(childSelectBoxObjectTempArray[childSelectBoxObjectArrayCount].id) != undefined)
                {
                    this.childSelectBoxObjectArray.push($(childSelectBoxObjectTempArray[childSelectBoxObjectArrayCount].id));
                }
            }

            this.refreshOptions();
        }
    },
    addChild : function(child)
    {
        // Obtain reference to child select box
        childSelectBoxObject = $(child);
        // Clone the dynamic select box
        selectBoxClone = childSelectBoxObject.cloneNode(true);
        // Obtain references to all cloned options
        clonedOptions = selectBoxClone.getElementsByTagName("option");

        //Arrays of Option Values, Text, and Class Attributes
        optionValueArray = new Array();
        optionTextArray = new Array();
        optionClassArray = new Array();
        optionIdArray = new Array();
        for (clonedOptionsCount=0;clonedOptionsCount<clonedOptions.length;clonedOptionsCount++)
        {
            optionValueArray.push(clonedOptions[clonedOptionsCount].value);
            optionTextArray.push(clonedOptions[clonedOptionsCount].text);
            optionClassArray.push(clonedOptions[clonedOptionsCount].className);
            optionIdArray.push(clonedOptions[clonedOptionsCount].id);
        }

        //Populate arrays
        this.childSelectBoxObjectArray.push(childSelectBoxObject);
        this.childSelectBoxObjectOptionValues.push(optionValueArray);
        this.childSelectBoxObjectOptionText.push(optionTextArray);
        this.childSelectBoxObjectOptionClass.push(optionClassArray);
        this.childSelectBoxObjectOptionId.push(optionIdArray);
    },
    refreshOptions : function()
    {
        // Create regular expression objects for "select" and the value
        // of the selected option of the main select box as class names
        pattern1 = /( |^)(select)( |$)/;
        if (this.useParentIdNotValue)
        {
            pattern2 = new RegExp("( |^)(" + this.parentSelectBox.options[this.parentSelectBox.selectedIndex].id + ")( |$)");
        }
        else
        {
            pattern2 = new RegExp("( |^)(" + this.parentSelectBox.options[this.parentSelectBox.selectedIndex].value + ")( |$)");
        }
        // EMPTY OPTION TO PREVENT SELECT FROM SHRINKING ON CHANGE
        pattern3 = /%placeholder%/;

        //Loop through childSelectBoxObjects
        for (childSelectBoxObjectArrayCount = 0; childSelectBoxObjectArrayCount < this.childSelectBoxObjectArray.length; ++childSelectBoxObjectArrayCount)
        {
            //CHECK TO MAKE SURE CHILD SELECT STILL EXISTS
            //MAY BE LOST IN AJAX REFRESHES OR OTHER EVENTS
            if ($(this.childSelectBoxObjectArray[childSelectBoxObjectArrayCount].id))
            {
                //TO PREVENT JS ERRORS IF CHILD SELECT FAILS TO POPULATE
                if (this.childSelectBoxObjectArray[childSelectBoxObjectArrayCount].options.length > 0)
                {
                    //OBTAIN THE CURRENT SELECTED INDEX
                    childSelectBoxSelectedIndexValue = this.childSelectBoxObjectArray[childSelectBoxObjectArrayCount].options[this.childSelectBoxObjectArray[childSelectBoxObjectArrayCount].selectedIndex].value;

                    //SET FIRST OPTION TO CURRENTLY SELECTED INDEX TO PREVENT BLINKING OFF OPTIONS
                    if (this.preserveSelectedIndexIndicator)
                    {
                        this.childSelectBoxObjectArray[childSelectBoxObjectArrayCount].options[0] =
                             new Option(
                                           this.childSelectBoxObjectArray[childSelectBoxObjectArrayCount].options[this.childSelectBoxObjectArray[childSelectBoxObjectArrayCount].selectedIndex].text
                                           ,this.childSelectBoxObjectArray[childSelectBoxObjectArrayCount].options[this.childSelectBoxObjectArray[childSelectBoxObjectArrayCount].selectedIndex].value
                                       );
                        this.childSelectBoxObjectArray[childSelectBoxObjectArrayCount].options[0].setAttribute('id',this.childSelectBoxObjectArray[childSelectBoxObjectArrayCount].options[this.childSelectBoxObjectArray[childSelectBoxObjectArrayCount].selectedIndex].id);
                    }
                    else
                    {
                        this.childSelectBoxObjectArray[childSelectBoxObjectArrayCount].options[0] = new Option('','');
                    }
                    this.childSelectBoxObjectArray[childSelectBoxObjectArrayCount].options[0].selected = true;

                    // Delete all options for the child select box
                    for (optionIterateCount = 0, optionCount = this.childSelectBoxObjectArray[childSelectBoxObjectArrayCount].length - 1; optionIterateCount < optionCount; ++optionIterateCount)
                    {
                        this.childSelectBoxObjectArray[childSelectBoxObjectArrayCount].remove(this.childSelectBoxObjectArray[childSelectBoxObjectArrayCount].length - 1);
                    }
                }

                // Iterate through cloned options
                for (childSelectBoxObjectOptionClassCount = 0, optionArrayCount = this.childSelectBoxObjectOptionClass[childSelectBoxObjectArrayCount].length; childSelectBoxObjectOptionClassCount < optionArrayCount; ++childSelectBoxObjectOptionClassCount)
                {
                    if
                    (
                        this.childSelectBoxObjectOptionClass[childSelectBoxObjectArrayCount][childSelectBoxObjectOptionClassCount].match(pattern1)
                        || this.childSelectBoxObjectOptionClass[childSelectBoxObjectArrayCount][childSelectBoxObjectOptionClassCount].match(pattern2)
                        || this.childSelectBoxObjectOptionClass[childSelectBoxObjectArrayCount][childSelectBoxObjectOptionClassCount].match(pattern3)
                    )
                    {
                        // Create the Option and add to select box
                        var optn = document.createElement("OPTION");
                        optn.text = this.childSelectBoxObjectOptionText[childSelectBoxObjectArrayCount][childSelectBoxObjectOptionClassCount];
                        optn.value = this.childSelectBoxObjectOptionValues[childSelectBoxObjectArrayCount][childSelectBoxObjectOptionClassCount];
                        optn.setAttribute('id',this.childSelectBoxObjectOptionId[childSelectBoxObjectArrayCount][childSelectBoxObjectOptionClassCount]);
                        optn.setAttribute('class',this.childSelectBoxObjectOptionClass[childSelectBoxObjectArrayCount][childSelectBoxObjectOptionClassCount]);
                        this.childSelectBoxObjectArray[childSelectBoxObjectArrayCount].options.add(optn);
                    }
                }
            }
        }

        var thisObj = this; //CLOSURE
        thisObj.execute();
    },
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
        for ( var functionCount=0; functionCount < this.fns.length; ++functionCount )
        {
            if ( this.fns[functionCount] !== fn )
            {
                tmpfns.push(this.fns[functionCount]);
            }
        }
        this.fns = tmpfns;
    },
    execute : function()
    {
        for ( var functionCount=0; functionCount < this.fns.length; ++functionCount )
        {
            this.fns[functionCount].call();
        }
    },
    //USED IN DEBUGING
    //CALL THIS TO MAKE SURE THE OBJECT HAS BEEN CREATED
    alert : function ()
    {
        alert("Working");
    }
}

