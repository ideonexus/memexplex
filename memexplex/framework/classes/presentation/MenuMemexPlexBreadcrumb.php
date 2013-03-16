<?php

/**
 * Appends the application menu to the begining of
 * an html block.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Dr. Clayton Forrester
 */
class MenuMemexPlexBreadcrumb extends Html
{

    /**
     * While the Framework can build its own breadcrumb based on the 
     * applicationmenu.xml, MemexPlex uses the same PageCodes for multiple
     * pages, rendering the MenuApplication->buildBreadCrumb useless.
     * 
     * This is a kluge to build the breadcrumb until a more elegant solution
     * can be devised. I'm on a January 1st deadline and can't waste a day
     * figuring this out.
     */
    public static function buildBreadCrumb($menuArray)
    {
        $breadCrumb = "";
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
            $breadCrumb = "<b>"
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
            $breadCrumb =
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
              . " <b>></b> <b>";

             if (PageConfiguration::getCurrentPageCode() == 'MemeList'
                 || PageConfiguration::getCurrentPageCode() == 'ReferenceList'
                 || PageConfiguration::getCurrentPageCode() == 'SchemaList'
                 || PageConfiguration::getCurrentPageCode() == 'TripleList')
             {
                 if (isset($_GET['domain'])
                     && $_GET['domain'] == 'curator')
                 {
                     $breadCrumb .=
                         "My ".str_replace("List","",PageConfiguration::getCurrentPageCode())."s";
                 }
                 else
                 {
                     $breadCrumb .=
                         "Public ".str_replace("List","",PageConfiguration::getCurrentPageCode())."s";
                 }
             }
             elseif (PageConfiguration::getCurrentPageCode() == 'Meme'
                 || PageConfiguration::getCurrentPageCode() == 'Reference'
                 || PageConfiguration::getCurrentPageCode() == 'Schema'
                 || PageConfiguration::getCurrentPageCode() == 'Triple')
             {
                 if (isset($_GET['id']))
                 {
                     $breadCrumb .=
                         "View ".PageConfiguration::getCurrentPageCode();
                 }
                 else
                 {
                     $breadCrumb .=
                         "Add ".PageConfiguration::getCurrentPageCode();
                 }
             }
             else
             {
                 $breadCrumb .=
                     htmlspecialchars(SimpleXml::getSimpleXmlItem
                     (
                         $menuArray
                         ,'menuItem'
                           . '/childMenuItem'
                           . '['
                           .     'pagecode='
                           .     '\'' . PageConfiguration::getCurrentPageCode() . '\''
                           . ']'
                           .     '/label'
                     ));
             }
             $breadCrumb .= "</b>";
             
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

            $breadCrumb =
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
        return $breadCrumb;
    }

}
