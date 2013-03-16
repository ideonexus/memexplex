<?php

/**
 * Builds the main XHTML content for the Bookmark page.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Doctor Ivo "Eggman" Robotnik
 * @see HtmlContent
 * @see HtmlContentInterface
 */
class HtmlContentBookmark extends HtmlContent
implements HtmlContentInterface
{
    /**
     * Builds the Bookmark page.
     */
    public function setSource()
    {
        $this->source .= "
<div style=\"width:525px;margin-left: auto;margin-right: auto;\">
Drag the button below to your bookmark bar. When you want to capture a new
meme from an online reference, http://yahoo.com/test/ select the text you 
wish to collect and click on the bookmark.
</div><br/>
";
        
        $this->source .=
            '<span class="pagination">'
            .'<a href="'
            .'javascript:function mxplx(){'
                .'var w=window'
                .',d=document'
                .",z=d.createElement('scr'+'ipt')"
                .',b=d.body'
                .',l=d.location'
                .',gst=function(){'
                    ."var t='';"
                    .'if(w.getSelection){'
                    	.'t=w.getSelection();'
                    .'}else if(d.getSelection){'
                        .'t=d.getSelection();'
        			.'}else if(d.selection){'
        			    .'t=d.selection.createRange().text;'
        			.'}return t;'
                .'};'
                .'try{'
                    .'if(!b)throw(0);'
                    .'s=gst();'
/**
 * [TODO: Figure out how to get this bookmarklet working. Currently the 
 * URIs are not encoding in Google Chrome, while IE encodes the URIs but
 * the browser does not recognize the link.]
 */
                    ."w.open('"
                        .ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                        .'Reference/'
                        ."m='+encodeURIComponent(s)+'"
                        ."&u='+encodeURIComponent(l.href)+'"
                        ."&t='+encodeURIComponent(d.title)"
                    .');'
        		.'}'
        		.'catch(e){'
        		    ."alert('Please wait until the page has loaded.');"
                .'}'
            .'}'
            .'mxplx();'
            .'void(0)'
            .'">Collect Meme</a>'
            .'</span>';
    }
}
