<?php

/**
 * If page has both view and edit versions, this will define the div tags for
 * them. This is intended to work with the MenuToggleViewEdit thingy.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma 04/05/2010
 * @see HtmlContent
 */
class HtmlViewEditContent extends HtmlContent
{

    /**
     * Sets the source.
     *
     * @param string $viewHtml View HTML code.
     * @param string $editHtml Edit HTML code.
     */
    public function setSource($viewHtml, $editHtml=false)
    {
        $this->source = "";

        $this->source .=
//SINGLE TABLE SURROUNDING ALL TABLES FOR FORMATING
"<div>"
    //DIV CONTAINER FOR VIEWABLE CONTENT
	."<div id=\"{$this->pageCode}_view\">"
    //GET VIEW VERSION OF TABLE
    .$viewHtml
	."</div>";
        //IF EDITABLE HTML, PROVIDE CONTENT
        if ($editHtml)
        {
            //DIV CONTAINER FOR EDITABLE CONTENT
            $this->source .=
	"<div id=\"{$this->pageCode}_edit\" style=\"display: none;\">"
        .$editHtml
	."</div>";
/**
 * [TODO: Revisit this. Commented it out, but it originally subscribed the
 * view/edit content function to the process form callback, but since I placed
 * the menus in the page objects using them, this is unneccessary.]
 */
            //DON'T RESUBSCRIBE THE TOGGLE IF
            //BEING POPULATED FROM AN AJAX CALL
//			if(!Constants::getConstant('AJAX_METHOD'))
//			{
//			    $this->source .=
//      "<script type=\"text/javascript\">"
//     . "var {$this->pageCode}SwitchContent = function()"
//     . "{"
//     .     "{$this->pageCode}ViewEditSwitchContent.switchContent"
//     .     "("
//     .         "'{$this->pageCode}_view','{$this->pageCode}_edit'"
//     .     ");"
//     . "};"
//     . "addLoadEvent(function()"
//     . "{"
//     .     "setTimeout(\""
//     .         "fillInTableCallbackObserver.subscribe("
//     .             "{$this->pageCode}SwitchContent"
//     .         ")\""
//     .     ",200);"
//     . "});"
//     . "</script>";
//
//			}
	    }
        $this->source .=
"</div>";
    }

}
