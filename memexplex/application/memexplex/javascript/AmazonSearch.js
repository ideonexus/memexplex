/**
 * Sets the selected index for a select field based on value
 * @param selectobject Select box.
 * @param value Value to select
 */
function setSelectedIndex(selectobject, value)
{
  for(index = 0; index < selectobject.length; index++) 
  {
	   if(selectobject[index].value == value)
	   {
		   selectobject.selectedIndex = index;
		   break;
	   }
  }
}

/**
 * Populates a reference form from the Amazon Search modal window.
 * 
 * @param index The result row being referenced.
 */
function populateReference(index)
{
	if (parent.$('referenceType').selectedIndex == 0)
	{
		if ($('categoryFilter').value == 'Books')
		{
			rst = '2';
			rt = '15';
		}
		else if ($('categoryFilter').value == 'DVD')
		{
			rst = '9';
			rt = '72';
		}
		else if ($('categoryFilter').value == 'Software')
		{
			rst = '9';
			rt = '82';
		}
		else if ($('categoryFilter').value == 'Music')
		{
			rst = '9';
			rt = '77';
		}
		setSelectedIndex(parent.$('referenceSuperType'),rst);
		parent.refreshDynamicSelectors();
		setSelectedIndex(parent.$('referenceType'),rt);
	}
	
	if (parent.$('authorLastName0').value == ""
		&& $('author'+index+'rows') != null)
	{
		for (j=0;j<$('author'+index+'rows').value;j++)
		{
			var lastName = '';
			var firstName = '';
			var $authorArray = $('author'+index+'_'+j).value.split(" ");
			lastNameSet = false;
			for (i=$authorArray.length;i>-1;i--)
			{
				if (typeof $authorArray[i] != 'undefined'
					&& $authorArray[i].replace(/^\s+|\s+$/g, '') != '')
				{
					if (
						$authorArray[i] == 'AOCN'
						|| $authorArray[i] == 'CNS'
							|| $authorArray[i] == 'C.N.S.'
						|| $authorArray[i] == 'DBA'
							|| $authorArray[i] == 'D.B.A.'
						|| $authorArray[i] == 'DSW'
							|| $authorArray[i] == 'D.S.W.'
						|| $authorArray[i] == 'Ed'
							|| $authorArray[i] == 'Ed.'
						|| $authorArray[i] == 'EdD'
							|| $authorArray[i] == 'Ed.D.'
						|| $authorArray[i] == 'FAAN'
						|| $authorArray[i] == 'FACP'
						|| $authorArray[i] == 'FACS'
						|| $authorArray[i] == 'JD'
							|| $authorArray[i] == 'J.D.'
						|| $authorArray[i] == 'LCSW'
						|| $authorArray[i] == 'LMFC'
						|| $authorArray[i] == 'LPC'
						|| $authorArray[i] == 'MA'
							|| $authorArray[i] == 'M.A.'
						|| $authorArray[i] == 'MaEd'
							|| $authorArray[i] == 'Ma.Ed.'
						|| $authorArray[i] == 'MBA'
						|| $authorArray[i] == 'MD'
							|| $authorArray[i] == 'M.D.'
						|| $authorArray[i] == 'MFCC'
						|| $authorArray[i] == 'MFCI'
						|| $authorArray[i] == 'MFTI'
						|| $authorArray[i] == 'MS'
							|| $authorArray[i] == 'M.S.'
						|| $authorArray[i] == 'MSN'
						|| $authorArray[i] == 'PhD'
							|| $authorArray[i] == 'Ph.D.'
						|| $authorArray[i] == 'PMP'
						|| $authorArray[i] == 'PsyD'
							|| $authorArray[i] == 'Psy.D.'
						|| $authorArray[i] == 'RN'
							|| $authorArray[i] == 'R.N.'
						|| $authorArray[i] == 'II'
						|| $authorArray[i] == 'III'
						|| $authorArray[i] == 'IV'
						|| $authorArray[i] == 'V'
						|| $authorArray[i] == 'VI'
						|| $authorArray[i] == 'VII'
						|| $authorArray[i] == 'VIII'
						|| $authorArray[i] == 'IX'
						|| $authorArray[i] == 'X'
					)
					{
						lastName = $authorArray[i]+" "+lastName;
					}
					else if (!lastNameSet)
					{
						lastName = $authorArray[i]+" "+lastName;
						lastNameSet = true;
					}
					else
					{
						firstName = $authorArray[i]+" "+firstName;
					}
				}
			}
			
			//Add row to parent window
			if (j > 0)
			{
				parent.addTableRow(
						'AuthorsTableEdit'
						,1
						,parseInt(parent.$('hidAuthorsTableEditRowCount').value)
				);
			}
			
			parent.$('authorLastName'+j).value  = lastName;
			parent.$('authorFirstName'+j).value = firstName;
		}
	}
	
	parent.$('largeImageDisplay').innerHTML        = '<img'
        +' src="'+$('largeImageUrl'+index).value+'"'
        +' height="184"'
        +' width="120"'
        +' />';

	if (parent.$('referenceTitle').value == "")
	{
		parent.$('referenceTitle').value               = $('title'+index).value;
	}
	
	if (parent.$('referenceDate').value == "")
	{
		parent.$('referenceDate').value                = $('publicationdate'+index).value;
	}
	
	if (parent.$('referencePublisherPeriodical').value == "")
	{
		parent.$('referencePublisherPeriodical').value = $('publisher'+index).value;
	}
	
	parent.$('referenceLargeImageUrl').value       = $('largeImageUrl'+index).value;
	parent.$('referenceSmallImageUrl').value       = $('smallImageUrl'+index).value;
	parent.$('referenceIsbn').value                = $('isbn'+index).value;
	parent.$('referenceEan').value                 = $('ean'+index).value;
	parent.$('referenceUpc').value                 = $('upc'+index).value;
	parent.$('asin').value                 		   = $('asin'+index).value;
	parent.$('amazonurl').value                    = $('amazonurl'+index).value;
	parent.hidePopWin();
}
