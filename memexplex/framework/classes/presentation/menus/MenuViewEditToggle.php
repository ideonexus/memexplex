<?php
/**
 * This a View/Edit Toggle button for a menu.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see Menu
 */

abstract class MenuViewEditToggle extends Menu
{

    /**
     * Builds javascript for flipping between view and edit modes.
     * @param string $toggleItem Human-readable label for what's being toggled.
     * @param string $pageCode Current pagecode.
     */
    protected function buildViewEditToggleMenu($toggleItem='',$pageCode='')
    {
//            if (Security::getEditPrivileges(PageConfiguration::getCurrentPageSecurityCode(),ApplicationSession::getValue('currentOrgId')))
//            {

            $this->source .=
"<script type=\"text/javascript\">"
."var update{$pageCode}Menu = function()"
."{"
.    "if ($(\"hidViewEdit{$pageCode}Menu\") != null)"
.    "{"
.        "if ($(\"hidViewEdit{$pageCode}Menu\").value == \"view\")"
.        "{"
.            "$(\"menu_view_edit{$pageCode}\").innerHTML = \"<a href=\\\"javascript:{$pageCode}ViewEditSwitchContent.switchContent('{$pageCode}_edit','{$pageCode}_view')\\\">Edit {$toggleItem}</a>\";"
.            "$(\"hidViewEdit{$pageCode}Menu\").value = \"edit\";"
.        "}"
.        "else if ($(\"hidViewEdit{$pageCode}Menu\").value == \"edit\")"
.        "{"
.            "$(\"menu_view_edit{$pageCode}\").innerHTML = \"<a href=\\\"javascript:{$pageCode}ViewEditSwitchContent.switchContent('{$pageCode}_view','{$pageCode}_edit')\\\">View {$toggleItem}</a>\";"
.            "$(\"hidViewEdit{$pageCode}Menu\").value = \"view\";"
.        "}"
.    "}"
."};"
."addLoadEvent(function()"
."{"
.     "{$pageCode}ViewEditSwitchContent = new SwitchContent;"
.     "{$pageCode}ViewEditSwitchContent.initialize();"
.     "{$pageCode}ViewEditSwitchContent.subscribe(update{$pageCode}Menu);"
.     "update{$pageCode}Menu();"
."});"
."</script>"
.    "<span id=\"menu_view_edit{$pageCode}\"></span>";

            //View by Default
            $this->source .=
    "<input type=\"hidden\" id=\"hidViewEdit{$pageCode}Menu\" name=\"hidViewEdit{$pageCode}Menu\" value=\"view\">";

            //INCLUDE SWITCH CONTENT JAVASCRIPT
            JavaScript::addJavaScriptInclude("switchContent");
//            }
    }

}
