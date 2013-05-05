//////////////////////////////////////////////////////////////////////////////
//                      Show/Hide With a Slide Script                       //
//////////////////////////////////////////////////////////////////////////////
// 
// This little script allows you to create a section of content and hide it at
// the top of your screen for users to open and close as they wish.  This is
// particularly handy for things like login boxes, supplementary navigation
// and content enhancements like tips, tricks and interesting tidbits of
// information you don't need showcased within your regular content.
//
// If a visitor has JavaScript disabled or unavailable, the hidden content box
// will simply display itself as if it was always a visible component.
//
// CONTRIBUTORS:
//
// Original Creator:
//     Paul Hirsch
//     www.paulhirsch.com
////////////////////////////////////////////////////////////////////////////////

var x = 0;
var showHt = "";
var hideHt = "";
var showVarHt = 0;
var hideVarHt = 0;
var show = null;
var hide = null;
var lastQuoteExpanded;
//These two adjust speed of slide
var y = 10; 
var z = 4;

function expandQuote(quoteid){
	showHt = "";
	hideHt = "";
	showVarHt = 0;
	hideVarHt = 0;
	show = 'quote'+quoteid;
	hide = 'quote'+lastQuoteExpanded;
	
	if ($(hide) != null){
		$(hide).style.display = 'none';
		//hideItem();
		$('expand'+lastQuoteExpanded).innerHTML = 'expand';
	}
	
	if (lastQuoteExpanded != quoteid){
		$(show).style.display = 'block';
		showHt = $(show).offsetHeight;
		$(show).style.height = '0px';
		showItem();
		$('expand'+quoteid).innerHTML = 'collapse';
		lastQuoteExpanded = quoteid;
	}
	else{
		lastQuoteExpanded = null;
	}
	
	$('meme'+quoteid).scrollIntoView(true);
}

function showItem() {
	$(show).style.height = showVarHt+'px';
	if (((showHt-showVarHt) < z) && (showVarHt !== showHt)) {
		showVarHt = showHt;
	} else {
		showVarHt = showVarHt+z;
	}
	if (showVarHt <= showHt) {
		setTimeout('showItem()',y);
	}
	if (showVarHt > showHt) {
		showVarHt = showHt;
	}
}

function hideItem() {
		$(hide).style.height = hideVarHt+'px';
		hideVarHt = hideVarHt-z;
		if ((hideHt-hideVarHt) <= hideHt) {
			setTimeout('hideItem()',y);
		}
		else
		{
			$(hide).style.display = 'none';
		}
		if ((hideHt-hideVarHt) > hideHt) {
			hideVarHt = 0;
			$(hide).style.height = hideVarHt+'px';
		}
}
