<?php

/**
 * Gets an HTMLPurifier instance.
 * http://htmlpurifier.org/docs
 * http://htmlpurifier.org/live/configdoc/plain.html
 *
 * Example of usage:
 *
 * $purifier = HtmlValidation::getHtmlPurifier();
 * $clean_html = $purifier->purify($dirty_html);
 *
*/
class HtmlValidation
{
    /**
     * 
     * An instance of HTMLPurifier
     * @var HTMLPurifier
     */
    protected static $_instance = NULL;
    
    /**
     * Uses singleton pattern.
     * 
     * Sets the configuration, instance static property, and returns
     * the HTMLPurifier object.
     */
    public static function getHtmlPurifier()
    {
        if(null !== self::$_instance){
            return self::$_instance;
        }

        require $_SERVER['DOCUMENT_ROOT']
            . ROOT_FOLDER
            . 'framework/plugins/htmlpurifier/HTMLPurifier.standalone.php';
        
        require $_SERVER['DOCUMENT_ROOT']
            . ROOT_FOLDER
            . 'framework/plugins/htmlpurifier/standalone/HTMLPurifier/Filter/YouTube.php';
        
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Doctype', 'XHTML 1.0 Strict'); // replace with your doctype
        $config->set(
        	'HTML.AllowedElements'
            ,'a, abbr, acronym, b, blockquote, br, caption, cite, code, dd, del, dfn, div, dl, dt, em, font, h1, h2, h3, h4, h5, h6, i, img, ins, kbd, li, ol, p, pre, s, span, strike, strong, sub, sup, table, tbody, td, tfoot, th, thead, tr, tt, u, ul, var'
        );
        $config->set(
        	'HTML.AllowedAttributes'
            ,'a.href, a.rev, a.title, a.target, a.rel, abbr.title, acronym.title, blockquote.cite, div.align, div.style, div.class, div.id, font.size, font.color, h1.style, h2.style, h3.style, h4.style, h5.style, h6.style, img.src, img.alt, img.title, img.class, img.align, img.style, ol.style, p.style, span.style, span.class, span.id, table.class, table.id, table.border, table.cellpadding, table.cellspacing, table.style, table.width, td.abbr, td.align, td.class, td.id, td.colspan, td.rowspan, td.style, td.valign, tr.align, tr.class, tr.id, tr.style, tr.valign, th.abbr, th.align, th.class, th.id, th.colspan, th.rowspan, th.style, th.valign, ul.style'
        );
        $config->set('Filter.YouTube', true);
        $config->set('AutoFormat.AutoParagraph', true);
        $config->set('AutoFormat.RemoveEmpty', true);
        
        self::$_instance = new HTMLPurifier($config);
        
        return self::$_instance;
    }
}
