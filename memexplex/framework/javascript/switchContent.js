/* 
 * This function turns one DIV tag on and another OFF.
 * Has an Observer pattern for modifying other objects.
 */

function SwitchContent() {}

SwitchContent.prototype =
{
    initialize : function()
    {
        //Initialize Arrays
        this.fns = [];
    },
    switchContent : function(show, hide)
    {
        if ($(show))
        {
            $(show).style.display = "";
        }
        if ($(hide))
        {
            $(hide).style.display = "none";
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