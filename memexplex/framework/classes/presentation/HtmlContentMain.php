<?php

/**
 * This is the main content for the page, displayed below header and menu.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma 08/21/2008
 * @see Html
 */
class HtmlContentMain extends Html
{

    /**
     * <todo:description>
     *
     * @param string $pageCode <todo:description>
     */
    public function setSource()
    {
        $application = Constants::getConstant('CURRENT_APPLICATION');
        $pageCode = PageConfiguration::getCurrentPageCode();

        $startReport =
"\n<!-- BEGIN REPORT -->\n"
."<a href=\"#\" name=\"dataTableAnchor\" id=\"dataTableAnchor\"></a>"
."<span id=\"dataTable\">";

        $reportObject = HtmlContentFactory::create($pageCode);
        $reportObject->setSource();
        $reportContent = $reportObject->getSource();

        $endReport =
"</span>";

        if (!Constants::getConstant('AJAX_METHOD'))
        {
            $endReport .=
 "<span id=\"loadingDisplay\" style=\"display:none\" class=\"largeBlue\">"
."<br/>"
."<p style=\"text-align:center\">Loading . . .<br/><br/>"
."<img src=\""
. ROOT_FOLDER
. "framework/images/loading.gif\" height=\"242\" width=\"242\" align=\"absmiddle\" alt=\"loading\" />"
. "</p>"
."</span>"
."<form name=\"report\" action=\"index.php\" method=\"post\">"
."<input type=\"hidden\" name=\"application\" value=\"{$application}\"/>"
."<input type=\"hidden\" name=\"pageCode\" value=\"{$pageCode}\"/>"
."<input type=\"hidden\" name=\"hidLoadingState\" value=\"\"/>"
."</form>";
        }

        $endReport .=
"\n<!-- END END REPORT -->\n";

        $this->source = $startReport . $reportContent . $endReport;

        Benchmark::setBenchmark('HtmlContentMain.php', __FILE__, __LINE__);
    }

}
