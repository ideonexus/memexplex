/** -----------------------------------------------------------------------
//   File Name: printPage.js
//     Created: 06/22/2010
//  Created By: Ryan Somma
// Description: Include this file for any page where a "Print" button is
//              provided.
//
//     History: 
//
// -----------------------------------------------------------------------*/

/**
 * Strips HREF tags from an html string.
 * Can be modified to strip other tags as well.
 */
function printFormat(html) 
{
    var container = document.createElement("div");
    container.innerHTML = html;
    var newHTML = container.innerHTML.toString();
    
    var anchors = container.getElementsByTagName("a");

    for (var i = 0; i < anchors.length; i++)
    {
        var hrefcontainer = document.createElement("div");
        hrefcontainer.appendChild(anchors[i].cloneNode(true));
        var href = hrefcontainer.innerHTML.toString();
        var text = anchors[i].innerHTML.toString();
        newHTML = newHTML.replace(href,text);
    }
    return newHTML;
}

/**
 * 
 * @param html: The block of html to be displayed in the print window.
 * @return none
 */
function openPrintView(html) 
{
    // specify window parameters
    printWin = window.open
    (
       ""
       ,"print"
       ,"width=600,height=450,status,scrollbars,resizable,screenX=20,screenY=40,left=20,top=40"
    );

    // wrote content to window
    printWin.document.write('<html><head><title>Print View:' + parent.$('headerpagetitle').innerHTML + '</title>');
    printWin.document.write('<link rel="Stylesheet" type="text/css" href="/framework/css/memexplex_style.css">');
    printWin.document.write('</head><body>');
    printWin.document.write('<span id="printcontrols" style="float:right;">');
    printWin.document.write('<a href="javascript:window.print();">print</a>');
    printWin.document.write(' | <a href="javascript:window.close();">close</a>');
    printWin.document.write('</span>');
    printWin.document.write('<div>');
    printWin.document.write(parent.$('headerapplication').innerHTML);
    printWin.document.write(parent.$('headerpagetitle').innerHTML);
    printWin.document.write(parent.$('dynamicHeader').innerHTML);
    printWin.document.write(parent.$('opfacDisplay').innerHTML);
    printWin.document.write(parent.$('headerdatetime').innerHTML);
    printWin.document.write('</div>');
    if (parent.$('headerData').innerHTML != '')
    {
        printWin.document.write('<div>');
        printWin.document.write(parent.$('headerData').innerHTML + ' ');
        printWin.document.write('</div>');
    }
    printWin.document.write(printFormat($(html).innerHTML));
    printWin.document.write('</body></html>');
    printWin.document.close();    
     
    printWin.focus();
 }
