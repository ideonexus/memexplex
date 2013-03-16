// -----------------------------------------------------------------------
//   File Name: timeFunctions.js
//     Created: 01/10/2001
//  Created By: Emil Moster
// Description: A collection of functions used on pages where there
//              exists time textboxes. 
//
//     History: 
//
// -----------------------------------------------------------------------


//TIME ROLLER + AND - ON NUMBER PAD TO CHANGE A DATE
  function TimeRoller(oSender)
  {
    if ((window.event.keyCode == 107 || window.event.keyCode == 109) && (oSender.value.length < 4))
    {
      var dTime;
      if (sDefaultTime != "")
      {
        var dTime = sDefaultTime;
      }
      oSender.value = dTime;
    }
    else if (window.event.keyCode == 107 || window.event.keyCode == 109)
    {
      var sValue = oSender.value
      var dTime = sValue;
      
      if (isNaN(dTime) || (Math.max(dTime,0) > 2359) || (Math.min(dTime,0) < 0))
      {
        var dTime;
        if (sDefaultTime != "")
        {
          var dTime = sDefaultTime;
        }
        oSender.value = dTime;
      }
      else if (window.event.keyCode == 107)
      {
        if ((Math.max(dTime.substr(2,2),0) + 1) == 60)
        {
          var dTime = LeadingZero(Math.max(dTime.substr(0,2),0) + 1) + "00";
        }
        else if (Math.max(dTime.substr(0,2),0) == 24)
        {
          if ((Math.max(dTime.substr(2,2),0) + 1) == 1)
          {
            var dTime = "0001";
          }
          else
          {
            var dTime = dTime.substr(0,2) + LeadingZero(Math.max(dTime.substr(2,2),0) + 1);
          }
        }
        else
        {
          var dTime = dTime.substr(0,2) + LeadingZero(Math.max(dTime.substr(2,2),0) + 1);
        }
        oSender.value = dTime;
      }
      else
      {
        var dTime = dTime.substr(0,2) + LeadingZero(Math.max(dTime.substr(2,2),0) - 1);
        oSender.value = dTime;
      }
    }
  }
