/**
 * Populates a reference form from the Amazon Search modal window.
 * 
 * @param index The result row being referenced.
 */
function populateTriple(subjectobject,memeid)
{
	parent.$(subjectobject+'Id').value = memeid;
	parent.$(subjectobject).innerHTML  = $('memeblock').innerHTML;
	parent.hidePopWin();
}
