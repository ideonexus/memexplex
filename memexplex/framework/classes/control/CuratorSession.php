<?php
/**
 * Functions for handling the Curator's current session.
 *
 * @package Framework
 * @subpackage Control
 * @author Ryan Somma
 */
class CuratorSession
{
    /**
     * @return boolean Whether the curator has add privileges.
     */
    public static function checkAddPrivileges()
    {
        if (ApplicationSession::isNameSet('CURATOR_ID'))
        {
            return true;
        }
        return false;
    }

    /**
     * @return boolean Whether the curator has edit privileges.
     */
    public static function checkEditPrivileges($uid=null)
    {
        if (ApplicationSession::isNameSet('CURATOR_ID')
            && (int) ApplicationSession::getValue('CURATOR_ID') == (int) $uid)
        {
            return true;
        }
        return false;
    }

    /**
     * Takes a Curator object and sets all session variables for it.
     *
     * @param Curator $curator
     * @return boolean
     */
    public static function setCuratorSession(Curator $curator)
    {
        ApplicationSession::setValue('CURATOR_ID',$curator->getId());
        ApplicationSession::setValue('CURATOR_EMAIL',$curator->getEmail());
        ApplicationSession::setValue('CURATOR_DISPLAY_NAME',$curator->getDisplayName());
        ApplicationSession::setValue('CURATOR_PASSWORD',$curator->getPassword());
        ApplicationSession::setValue('CURATOR_PUBLISH_BY_DEFAULT',$curator->getPublishByDefault());
        ApplicationSession::setValue('CURATOR_LEVEL_ID',$curator->getLevel()->getId());
        ApplicationSession::setValue('CURATOR_LEVEL_DESCRIPTION',$curator->getLevel()->getDescription());
        PageSession::unSetName('menuhtml', 'all');
        return true;
    }

    /**
     * Builds a Curator object from current session values.
     *
     * @return Curator
     */
    public static function getCuratorFromSession()
    {
        $curator = null;
        if (ApplicationSession::isNameSet('CURATOR_ID'))
        {
            $curator =
                new Curator
                (
                     ApplicationSession::getValue('CURATOR_ID')
                	,ApplicationSession::getValue('CURATOR_EMAIL')
                    ,ApplicationSession::getValue('CURATOR_DISPLAY_NAME')
                    ,ApplicationSession::getValue('CURATOR_PASSWORD')
                    ,ApplicationSession::getValue('CURATOR_PUBLISH_BY_DEFAULT')
                    ,new CuratorLevel(ApplicationSession::getValue('CURATOR_LEVEL_ID'))
                );
        }
        return $curator;
    }

    /**
     * Takes a Curator object and sets cookie values for it.
     *
     * @param Curator $curator
     * @return boolean
     */
    public static function setCuratorCookie(Curator $curator)
    {
	    $expires = time()+60*60*24*30;
        setcookie("CURATOR_ID", $curator->getId(), $expires,"/",APPLICATION_DOMAIN);
        setcookie("CURATOR_EMAIL", $curator->getEmail(), $expires,"/",APPLICATION_DOMAIN);
        setcookie("CURATOR_DISPLAY_NAME", $curator->getDisplayName(), $expires,"/",APPLICATION_DOMAIN);
        setcookie("CURATOR_PASSWORD", $curator->getPassword(), $expires,"/",APPLICATION_DOMAIN);
        setcookie("CURATOR_PUBLISH_BY_DEFAULT", $curator->getPublishByDefault(), $expires,"/",APPLICATION_DOMAIN);
        setcookie("CURATOR_LEVEL_ID", $curator->getLevel()->getId(), $expires,"/",APPLICATION_DOMAIN);
        setcookie("CURATOR_LEVEL_DESCRIPTION", $curator->getLevel()->getDescription(), $expires,"/",APPLICATION_DOMAIN);
        return true;
    }

    /**
     * Validates the curator session or populates the curator
     * session from a cookie
     *
     * @return boolean
     */
    public static function validateCuratorSession()
    {
        if (isset($_GET['pageCode'])
            && $_GET['pageCode'] == "Logout")
        {
            self::unsetCuratorSession();
            header
            (
                'Location: '
                . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                . 'MemeList/'
            );
            exit;
        }
        /**
         * If Curator session is not set, but a cookie exists.
         * Set the curator session from the existing cookie.
         */
        elseif (!ApplicationSession::isNameSet('CURATOR_ID')
            && isset($_COOKIE['CURATOR_ID']))
        {
			/**
             * [TODO] This should probably check the password in
             * the cookie against the password in the database
             * in case it's possible to spoof a cookie or something.
             */
            ApplicationSession::setValue('CURATOR_ID',$_COOKIE['CURATOR_ID']);
            ApplicationSession::setValue('CURATOR_EMAIL',$_COOKIE['CURATOR_EMAIL']);
            ApplicationSession::setValue('CURATOR_DISPLAY_NAME',$_COOKIE['CURATOR_DISPLAY_NAME']);
            ApplicationSession::setValue('CURATOR_PASSWORD',$_COOKIE['CURATOR_PASSWORD']);
            ApplicationSession::setValue('CURATOR_PUBLISH_BY_DEFAULT',$_COOKIE['CURATOR_PUBLISH_BY_DEFAULT']);
            ApplicationSession::setValue('CURATOR_LEVEL_ID',$_COOKIE['CURATOR_LEVEL_ID']);
            ApplicationSession::setValue('CURATOR_LEVEL_DESCRIPTION',$_COOKIE['CURATOR_LEVEL_DESCRIPTION']);
            //DEFAULT TO CURATOR'S DOMAIN
            ApplicationSession::setValue('DOMAIN','curator');
            return true;
        }
        /**
         * If Curator Session is set, and there's a GET variable
         * indicating to set a cookie, then give the curator what
         * they want!
         */
        elseif(ApplicationSession::isNameSet('CURATOR_ID')
            && isset($_GET['setcookie'])
            && $_GET['setcookie'] == 'true')
        {
            self::unsetCuratorCookie();

            $expires = time()+60*60*24*30;
            setcookie("CURATOR_ID", ApplicationSession::getValue('CURATOR_ID'), $expires,"/",APPLICATION_DOMAIN);
            setcookie("CURATOR_EMAIL", ApplicationSession::getValue('CURATOR_EMAIL'), $expires,"/",APPLICATION_DOMAIN);
            setcookie("CURATOR_DISPLAY_NAME", ApplicationSession::getValue('CURATOR_DISPLAY_NAME'), $expires,"/",APPLICATION_DOMAIN);
            setcookie("CURATOR_PASSWORD", ApplicationSession::getValue('CURATOR_PASSWORD'), $expires,"/",APPLICATION_DOMAIN);
            setcookie("CURATOR_PUBLISH_BY_DEFAULT", ApplicationSession::getValue('CURATOR_PUBLISH_BY_DEFAULT'), $expires,"/",APPLICATION_DOMAIN);
            setcookie("CURATOR_LEVEL_ID", ApplicationSession::getValue('CURATOR_LEVEL_ID'), $expires,"/",APPLICATION_DOMAIN);
            setcookie("CURATOR_LEVEL_DESCRIPTION", ApplicationSession::getValue('CURATOR_LEVEL_DESCRIPTION'), $expires,"/",APPLICATION_DOMAIN);
            return true;
        }
    }

    /**
     * Destroys the current curator session.
     */
    public static function unsetCuratorSession()
    {
        ApplicationSession::unSetName('CURATOR_ID');
        ApplicationSession::unSetName('CURATOR_EMAIL');
        ApplicationSession::unSetName('CURATOR_DISPLAY_NAME');
        ApplicationSession::unSetName('CURATOR_PASSWORD');
        ApplicationSession::unSetName('CURATOR_PUBLISH_BY_DEFAULT');
        ApplicationSession::unSetName('CURATOR_LEVEL_ID');
        ApplicationSession::unSetName('CURATOR_LEVEL_DESCRIPTION');
        ApplicationSession::unSetName('DOMAIN');
        PageSession::unSetName('menuhtml', 'all');
        self::unsetCuratorCookie();
    }

    /**
     * Destroys the current curator cookie
     */
    public static function unsetCuratorCookie()
    {
        if (isset($_COOKIE['CURATOR_ID']))
        {
            $domain = "." . str_replace("www.","",$_SERVER['HTTP_HOST']);

            $expires = time() - 3600;
            setcookie("CURATOR_ID", "", $expires,"/",APPLICATION_DOMAIN);
            setcookie("CURATOR_EMAIL", "", $expires,"/",APPLICATION_DOMAIN);
            setcookie("CURATOR_DISPLAY_NAME", "", $expires,"/",APPLICATION_DOMAIN);
            setcookie("CURATOR_PASSWORD", "", $expires,"/",APPLICATION_DOMAIN);
            setcookie("CURATOR_PUBLISH_BY_DEFAULT", "", $expires,"/",APPLICATION_DOMAIN);
            setcookie("CURATOR_LEVEL_ID", "", $expires,"/",APPLICATION_DOMAIN);
            setcookie("CURATOR_LEVEL_DESCRIPTION", "", $expires,"/",APPLICATION_DOMAIN);
            
            /**[TODO: Remove this after 03/10/2011]*/
            setcookie("CURATOR_ID", "", $expires,"/");
            setcookie("CURATOR_EMAIL", "", $expires,"/");
            setcookie("CURATOR_DISPLAY_NAME", "", $expires,"/");
            setcookie("CURATOR_PASSWORD", "", $expires,"/");
            setcookie("CURATOR_PUBLISH_BY_DEFAULT", "", $expires,"/");
            setcookie("CURATOR_LEVEL_ID", "", $expires,"/");
            setcookie("CURATOR_LEVEL_DESCRIPTION", "", $expires,"/");
        }
    }
}
