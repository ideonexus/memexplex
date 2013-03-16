<?php

/**
 * Appends the application menu to the begining of
 * an html block.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 */
class MenuApplication extends Html
{

    /**
     * Like Hansel and Gretel.
     * @var string
     */
    protected $breadCrumb;

    /**
     * Builds the breadcrum for the page.
     */
    private function buildBreadCrumb($menuArray)
    {
        //TOP-LEVEL MENU ITEM
        if
        (
            SimpleXml::getSimpleXmlItem
            (
                $menuArray
                ,'menuItem'
                  . '['
                  .     'pagecode='
                  .     '\'' . PageConfiguration::getCurrentPageCode() . '\''
                  . ']'
                  .     '/label'
             )
        )
        {
            $this->breadCrumb = "<b>"
                  .  htmlspecialchars(SimpleXml::getSimpleXmlItem
                     (
                         $menuArray
                         ,'menuItem'
                           . '['
                           .     'pagecode='
                           .     '\'' . PageConfiguration::getCurrentPageCode() . '\''
                           . ']'
                           .     '/label'
                     ))
                  . "</b>";
        }
        //IF CHILD-MENU-ITEM
        else if
        (
            $menuArray->xpath
            (
                'menuItem'
                . '/childMenuItem'
                . '['
                .     'pagecode='
                .     '\'' . PageConfiguration::getCurrentPageCode() . '\''
                . ']'
                .     '/label'
            )
        )
        {
             $this->breadCrumb =
                     htmlspecialchars(SimpleXml::getSimpleXmlItem
                     (
                         $menuArray
                         ,'menuItem'
                         . '/childMenuItem'
                         . '['
                         .     'pagecode='
                         .     '\'' . PageConfiguration::getCurrentPageCode() . '\''
                         . ']'
                         .'/parent::*'
                         .     '/label'
                     ))
                  . " <b>></b> <b>"
                  .  htmlspecialchars(SimpleXml::getSimpleXmlItem
                     (
                         $menuArray
                         ,'menuItem'
                           . '/childMenuItem'
                           . '['
                           .     'pagecode='
                           .     '\'' . PageConfiguration::getCurrentPageCode() . '\''
                           . ']'
                           .     '/label'
                     ))
                  . "</b>";
        }
        //IF CHILD-CHILD-MENU-ITEM
        else if
        (
            $menuArray->xpath
            (
                'menuItem'
                .   '/childMenuItem'
                .       '/childChildMenuItem'
                .       '['
                .           'pagecode='
                .           '\'' . PageConfiguration::getCurrentPageCode() . '\''
                .       ']'
                .           '/label'
            )
        )
        {
            switch
            (
                SimpleXml::getSimpleXmlItem
               (
                    $menuArray
                    ,'menuItem'
                    .   '/childMenuItem'
                    .       '/childChildMenuItem'
                    .       '['
                    .           'pagecode='
                    .           '\'' . PageConfiguration::getCurrentPageCode() . '\''
                    .       ']'
                    .   '/parent::*'
                    .       '/system'
                 )
            )
            {
                case 'ASP':
                    //SET TO MENUFORM SUBMIT TO MAINTAIN CURATOR SESSION
                    $childLink = "javascript:MenuFormSubmit('"
                                 . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                                 . "PhpAsp/"
                                 . "','menuNavigation','"
                                 . SimpleXml::getSimpleXmlItem
                                   (
                                        $menuArray
                                        ,'menuItem'
                                        .   '/childMenuItem'
                                        .       '/childChildMenuItem'
                                        .       '['
                                        .           'pagecode='
                                        .           '\'' . PageConfiguration::getCurrentPageCode() . '\''
                                        .       ']'
                                        .   '/parent::*'
                                        .       '/link'
                                     )
                                 . "')";
                    break;
                case 'PHP':
                    $childLink = ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                                 . SimpleXml::getSimpleXmlItem
                                   (
                                        $menuArray
                                        ,'menuItem'
                                        .   '/childMenuItem'
                                        .       '/childChildMenuItem'
                                        .       '['
                                        .           'pagecode='
                                        .           '\'' . PageConfiguration::getCurrentPageCode() . '\''
                                        .       ']'
                                        .   '/parent::*'
                                        .       '/link'
                                     );
                    break;
                default:
                    $childLink = SimpleXml::getSimpleXmlItem
                                   (
                                        $menuArray
                                        ,'menuItem'
                                        .   '/childMenuItem'
                                        .       '/childChildMenuItem'
                                        .       '['
                                        .           'pagecode='
                                        .           '\'' . PageConfiguration::getCurrentPageCode() . '\''
                                        .       ']'
                                        .   '/parent::*'
                                        .       '/link'
                                     );
            }

            $this->breadCrumb =
                   htmlspecialchars(SimpleXml::getSimpleXmlItem
                   (
                        $menuArray
                        ,'menuItem'
                        .   '/childMenuItem'
                        .       '/childChildMenuItem'
                        .       '['
                        .           'pagecode='
                        .           '\'' . PageConfiguration::getCurrentPageCode() . '\''
                        .       ']'
                        .   '/parent::*'
                        .'/parent::*'
                        .   '/label'
                     ))
                . " <b>></b> <a href=\""
                .  $childLink
                . "\">"
                .  htmlspecialchars(SimpleXml::getSimpleXmlItem
                   (
                        $menuArray
                        ,'menuItem'
                        .   '/childMenuItem'
                        .       '/childChildMenuItem'
                        .       '['
                        .           'pagecode='
                        .           '\'' . PageConfiguration::getCurrentPageCode() . '\''
                        .       ']'
                        .   '/parent::*'
                        .       '/label'
                     ))
                . "</a> <b>></b> <b>"
                .  htmlspecialchars(SimpleXml::getSimpleXmlItem
                   (
                        $menuArray
                        ,'menuItem'
                        .   '/childMenuItem'
                        .       '/childChildMenuItem'
                        .       '['
                        .           'pagecode='
                        .           '\'' . PageConfiguration::getCurrentPageCode() . '\''
                        .       ']'
                        .           '/label'
                     ))
                . "</b>";
        }
    }

    /**
     * Sets the application menu source once from the applicationmenu.xml
     * file, once loaded into session, doesn't load it again.
     */
    public function setSource()
    {
        if ('none' != PageConfiguration::getCurrentPageApplicationMenu())
        {

            //RETRIEVE MENU LAYOUT FROM XML FILE
            // GET THE PAGE PROPERTIES FROM THE XML FILE
            if (!isset($_SESSION['applicationMenu']))
            {
                //AND SERIALIZE INTO SESSION VARIABLE
                try
                {
                    $_SESSION['applicationMenu'] = serialize
                                           (
                                               new SimpleXMLSessioned
                                               (
                                                   $_SERVER['DOCUMENT_ROOT']
                                                   . ROOT_FOLDER
                                                   . 'application/'
                                                   . Constants::getConstant('CURRENT_APPLICATION')
                                                   . '/config/applicationmenu.xml'
                                               )
                                           );
                }
                catch (GeneralException $e)
                {
                    throw new PresentationExceptionConfigurationError('Menu not found.');
                }
            }
            $menuArrayXml = unserialize($_SESSION['applicationMenu']);
            $menuArray = $menuArrayXml->getSimpleXMLObject();

            if (PageSession::isNameSet('menuhtml', 'all'))
            {
                $this->source = PageSession::getValue('menuhtml', 'all');
            }
            else
            {
                $divider = "";
                $pageCodeFound = false;
                $this->source = "\n<!-- BEGIN MENU INCLUDE -->\n"
                                 . "<ul id=\"menu\">";
                $secondLevelMenu = "";

                // BUILD TOP-LEVEL MENU ITEMS
                foreach($menuArray->menuItem as $menuItem)
                {
                    $link = "";
                    $label = htmlspecialchars($menuItem->label);
                    $icon = "";
                    if ($menuItem->icon)
                    {
                        $icon = 
                        	'<img src="'.ROOT_FOLDER.'framework/images/'
                            .$menuItem->icon.'" width="13" height="13" border="0"/>&nbsp;';
                    }
                    
                    if($menuItem->childMenuItem != ''
                        && ApplicationSession::isNameSet('CURATOR_ID'))
                    {
                        $secondLevelMenu = "";
                        $link = "javascript:void(0);";//"javascript:showhide('menu_"
                                //. str_replace(' ','_',$menuItem->label) . "')";
                        $onclick     = " onclick=\"unlockMenu('menu_"
                                       . str_replace(' ','_',$menuItem->label)
                                       . "');\"";
                        $onmouseover = " onmouseover=\"openDropdown('menu_"
                                       . str_replace(' ','_',$menuItem->label)
                                       . "');\"";
                        $onmouseout  = " onmouseout=\"closeDropdownTimeOut();\"";

                        // BUILD SECOND-LEVEL, DIV-HIDDEN MENU ITEMS
                        $secondLevelDivider = "";  //"<b>" . $menuItem->label . " Menu:</b> ";
                        $secondLevelMenu .= "<br /><div id=\"menu_"
                                            . str_replace(' ','_', $menuItem->label)
                                            . "\""
                                            . " onmouseover=\"cancelCloseDropdownTimeOut()\""
                                            . " onmouseout=\"closeDropdownTimeOut()\""
                                            . ">";
                                            //. " style=\"display: none;\">";

                        foreach($menuItem->childMenuItem as $childMenuItem)
                        {
                            $showChildMenuItem = false;
//                            if
//                            (
//                                $childMenuItem->developer
//                                || $childMenuItem->appadmin
//                                || $childMenuItem->security
//                            )
//                            {
//                                if
//                                (
//                                    ($childMenuItem->developer && Security::developerCheck())
//                                    || ($childMenuItem->appadmin && Security::adminCheck())
//                                    ||
//                                    (
//                                        $childMenuItem->security
//                                        && Security::getViewPrivileges($childMenuItem->security)
//                                    )
//                                )
//                                {
//                                    $showChildMenuItem = true;
//                                }
//                            }
//                            else
//                            {
                                $showChildMenuItem = true;
//                            }
                            if ($showChildMenuItem)
                            {
                                //SET THE BASE URL FOR PHP VS ASP APPLICATIONS
                                $childLink = '';
                                $childLink = ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                                             . $childMenuItem->link;
                                $secondLevelMenu .= $secondLevelDivider
                                                    . "<a href=\"" . $childLink . "\">"
                                                    . htmlspecialchars($childMenuItem->label)
                                                    . "</a>";
                                //$secondLevelDivider = "&nbsp;|&nbsp;";
                            }
                        }
                        $secondLevelMenu .= "</div>";
                    }
                    else
                    {
                        $secondLevelMenu = "";
                        //SET THE BASE URL FOR PHP VS ASP APPLICATIONS
                        $link = "";
                        $onclick = "";
                        $onmouseout = "";
                        $onmouseover = "";
                        $target = "";
                        $name = "";
                        $id = "";
                        $spanOpen = "";
                        $spanClose = "";
                        switch($menuItem->system)
                        {
                            case 'LoggedIn':
                                $link = "";
                            case 'COMMUNITY MESSAGE':
                                $label = "";
                                $spanOpen = "<span id=\"aCommunityMessageDivider\""
                                            . " style=\"display:none;\">";
                                $link = "javascript:ToggleCommunityMessage();";
                                $id = " id=\"aCommunityMessageLink\"";
                                $spanClose = "</span>";
                                break;
                            default:
                                $link = ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                                             . $menuItem->link;
                        }
                    }

                    $align = "";
                    if
                    (
                        $menuItem->label == 'Login'
                        || $menuItem->label == 'Sign Up'
                        || $menuItem->label == 'Logout'
                    )
                    {
                        $align = " style=\"float:right;\"";
                        $divider = "";
                        if (ApplicationSession::isNameSet('CURATOR_DISPLAY_NAME'))
                        {
                            if ($menuItem->label == 'Sign Up'
                                || $menuItem->label == 'Login')
                            {
                                $link = '';
                            }
                            elseif ($menuItem->label == 'Logout')
                            {
                                $divider = "Greetings, " . ApplicationSession::getValue('CURATOR_DISPLAY_NAME') . "&nbsp;/&nbsp;";
                            }
                        }
                        else
                        {
                            if ($menuItem->label == 'Sign Up')
                            {
                                $divider = "&nbsp;/&nbsp;";
                            }
                            elseif ($menuItem->label == 'Logout')
                            {
                                $link = '';
                            }
                        }
                    }

                    if ($link != '')
                    {
                        $this->source .= "<li" . $align . ">" . $spanOpen . $divider . $spanClose . "<a href=\"" . $link
                        . "\"" . $onclick . $onmouseout . $onmouseover . $target . $name . $id . ">" 
                        . $icon
                        . $label
                        . "</a>"
                        . $secondLevelMenu . "</li>";

                        $divider = "&nbsp;|&nbsp;";
                    }
                }

                PageSession::setValue('menuhtml', $this->source, 'all');
            }

            //$this->buildBreadCrumb($menuArray);

            $this->source .= "</ul><br />";
            //$this->source .= $this->breadCrumb;
            //$this->source .= MenuMemexPlexBreadcrumb::buildBreadCrumb($menuArray);
            $this->source .= "<br />";
            $this->source .= "<iframe id=\"divframe\" src=\"\" class=\"frmcls\"></iframe>"
                           . "<form name=\"frmMenu\" action=\"\" method=\"post\">"
                           . "<input type=\"hidden\" name=\"hidParam1\" value=\"\" />"
                           . "<input type=\"hidden\" name=\"hidParam2\" value=\"\" />"
                           . "<input type=\"hidden\" name=\"hidParam3\" value=\"\" />"
                           . "<input type=\"hidden\" name=\"hidParam4\" value=\"\" />"
                           . "<input type=\"hidden\" name=\"hidParam5\" value=\"\" />"
                           . "</form>"
                           . "\n<!-- END MENU INCLUDE -->\n";
            $this->source .= $this->externalSource;

            Benchmark::setBenchmark('MenuApplication.php', __FILE__, __LINE__);
        }
    }

}
