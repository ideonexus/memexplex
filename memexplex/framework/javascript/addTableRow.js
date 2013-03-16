/*
 * http://www.webdeveloper.com/forum/showthread.php?t=63916
 * Use the "cloneTableRow" method onload to grab a copy of the
 * last blank row in the table. Use the "addTableRow" method to
 * add the clone.
 * This procedure grabs all "input", "select", and "a" tags, copies thier
 * names and ids and increments them +1. Other attributes may need
 * modification in the future. This function cannot handle javascript
 * onclick variables, so scripts must find other methods to reference
 * their rows, such as "hidden" inputs, "a" tags, or the "this" keyword.
 */

//Observer object for executing other scripts based on
//addTableRow execution (ie. registering new form elements for validation)
var cloneTableRowObserver = new Observer;

var root;
var clonedRows;
function cloneTableRow(tableToClone,rowsToClone,originalAttributeIndex)
{
    //the root
    root = $(tableToClone).tBodies[0];
    //clonedRows array
    clonedRows = new Array();
    //the rows' collection
    var allRows = root.getElementsByTagName('tr');

    if (rowsToClone == null)
    {
        rowsToClone = 1;
    }

    if (originalAttributeIndex == null)
    {
        originalAttributeIndex = ((allRows.length-1)/rowsToClone)-1;
    }
    newAttributeIndex = (originalAttributeIndex + 1)

    for (j=0;j<rowsToClone;j++)
    {
        //the clone of the last row
        cRow = allRows[(allRows.length-(1+j))].cloneNode(true);

        //change the tr id
        if (cRow.getAttribute('id') != null)
        {
            cRow.setAttribute('id',cRow.getAttribute('id').replace(originalAttributeIndex, newAttributeIndex));
        }

        //Rename all element attributes
        var cElements = cRow.getElementsByTagName('*');
        for(i=0;i<cElements.length;i++)
        {
            //changes the inputs name and id
            if (cElements[i].tagName != "OPTION")
            {
                if (cElements[i].getAttribute('name') != null && cElements[i].getAttribute('name') != '')
                {
                    cElements[i].setAttribute('name',cElements[i].getAttribute('name').replace(originalAttributeIndex, newAttributeIndex));
                }
                if (cElements[i].getAttribute('id') != null && cElements[i].getAttribute('id') != '')
                {
                    cElements[i].setAttribute('id',cElements[i].getAttribute('id').replace(originalAttributeIndex, newAttributeIndex));
                }
                //BLANK OUT VALUE
                if (cElements[i].type != "checkbox" && cElements[i].getAttribute('value') != null && cElements[i].getAttribute('value') != '')
                {
                    cElements[i].setAttribute('value','');
                }
                cElements[i].disabled = false;
            }
        }

        clonedRows[j] = cRow;
    }
    cloneTableRowObserver.execute();
}

//Observer object for executing other scripts based on
//addTableRow execution (ie. registering new form elements for validation)
var addTableRowObserver = new Observer;

function addTableRow(tableToModify,rowsToClone,originalAttributeIndex)
{
    if ($(tableToModify) != null)
    {
        //the root
        root = $(tableToModify).tBodies[0];

        //appends the cloned row as a new row
        for ( var i=0, j=clonedRows.length; i < j; ++i )
        {
            root.appendChild(clonedRows[((j-1) - i)]);
        }
        //because a new row has been added to the table,
        //increment the attributeindex by one.
        originalAttributeIndex++;
        //clone the next blank row for next 'more'
        cloneTableRow(tableToModify,rowsToClone,originalAttributeIndex);
        addTableRowObserver.execute();
    }
}
