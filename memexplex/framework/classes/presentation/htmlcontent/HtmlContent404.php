<?php

/**
 * HtmlContent for the Page Not Found error page.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see HtmlContent
 * @see HtmlContentInterface
 */
class HtmlContent404 extends HtmlContent
implements HtmlContentInterface
{

    /**
     * Sets the HTML source.
     */
    public function setSource()
    {
            $this->source .=
"<!-- BEGIN 404 -->"
.'<div align="center">'
.'<img src="'.ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS').'framework/images/404.jpg" border="1" width="760" height="430" alt="Semantology">'
."<div class=\"largeBlue\">"
."<br/><h2><i>Please Try Again</i></h2>"
."</div>"
.'<br/>Image Credit: "Semantology" by <a href="http://www.flickr.com/photos/adactio/2271761903/">Jeremy Keith</a>' 
.'<br/><br/></div>'
."<!-- END 404 -->";
    }

}
