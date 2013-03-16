<?php

    /**
     * This class manages the Session variables for the
     * current application. Acts like a namespace.
     *
     * @package Framework
     * @subpackage Control
     * @author Craig Avondo
     * @author Ryan Somma
 	*/

    class ApplicationSession extends Session
    {

        /**
         * @var string The current application namespace.
         */
        protected static $currentNameSpace;

        /**
         * VALID APPLICATION NAMESPACES
         */
        const APPLICATION_MEMEXPLEX = 'memexplex';

        /**
         * VALID APPLICATION SESSION NAMES
         */
        const PAGE_CODE                   = 'pageCode';
        const DEBUG_FLAG                  = 'debugFlag';
        const COMMUNITY_MESSAGE           = 'communityMessage';
        const DISPLAY_COMMUNITY_MESSAGE   = 'displayCommunityMessage';

        /**
         * Curator Session Items
         */
        const CURATOR_ID                = 'CURATOR_ID';
        const CURATOR_EMAIL             = 'CURATOR_EMAIL';
        const CURATOR_DISPLAY_NAME      = 'CURATOR_DISPLAY_NAME';
        const CURATOR_PASSWORD          = 'CURATOR_PASSWORD';
        const CURATOR_PUBLISH_BY_DEFAULT = 'CURATOR_PUBLISH_BY_DEFAULT';
        const CURATOR_LEVEL_ID          = 'CURATOR_LEVEL_ID';
        const CURATOR_LEVEL_DESCRIPTION = 'CURATOR_LEVEL_DESCRIPTION';

        /**
         * Environment Session Items
         */
        const DOMAIN                    = 'DOMAIN';
        const CURRENT_APPLICATION_DIRECTORY         = 'CURRENT_APPLICATION_DIRECTORY';
        const HEADER_BACKGROUND_COLOR               = 'HEADER_BACKGROUND_COLOR';
        const ENVIRONMENT_DISPLAY_NAME              = 'ENVIRONMENT_DISPLAY_NAME';
        const CURRENT_PHP_APPLICATION_WEB_ADDRESS   = 'CURRENT_PHP_APPLICATION_WEB_ADDRESS';
        const CURRENT_PHP_APPLICATION_LOG_DIRECTORY = 'CURRENT_PHP_APPLICATION_LOG_DIRECTORY';

        /**
         * Sets the current application namespace (ie. memexplex)
         *
         * @param string $nameSpace Subdirectory folder name under application directory.
         */
        public static function initialize($nameSpace = CURRENT_APPLICATION)
        {
            if (self::_validateNameSpace($nameSpace))
            {
                self::$currentNameSpace = $nameSpace;
            }
        }

        /**
         * Gets the value of a session variable.
         *
         * @param string $name Session variable name.
         * @return mixed Session variable value.
         */
        public static function getValue($name)
        {
            if
            (
                self::_validateName($name)
                && parent::_sessionIsset(self::$currentNameSpace, $name)
            )
            {
                return parent::_sessionGet(self::$currentNameSpace, $name);
            }
        }

        /**
         * Sets the value of a session variable.
         *
         * @param string $name Session variable name.
         * @param mixed $value Session variable value.
         */
        public static function setValue($name, $value)
        {
            if (self::_validateName($name))
            {
                parent::_sessionSet(self::$currentNameSpace, $name, $value);
            }
        }

        /**
         * Checks if a session variable is set.
         *
         * @param string $name Name of session variable.
         * @return bool Name is set or no.
         */
        public static function isNameSet($name)
        {
            return parent::_sessionIsset(self::$currentNameSpace, $name);
        }

        /**
         * Unset a session variable in the current namespace.
         *
         * @param string $name Name of variable to unset
         */
        public static function unSetName($name)
        {
            if (self::_validateName($name))
            {
                parent::_sessionUnset(self::$currentNameSpace, $name);
            }
        }

        /**
         * Unsets all session variables for the current namespace.
         */
        public static function unsetAll()
        {
            parent::_sessionNamespaceUnset(self::$currentNameSpace);
        }

        /**
         * Validates the session variable name.
         *
         * @param string $name Name of the session variable.
         * @return bool Valid or No.
         */
        public static function validateName($name)
        {
            switch ($name)
            {
                case self::PAGE_CODE:
                    // FALL THROUGH
                case self::DEBUG_FLAG:
                    // FALL THROUGH
                case self::CURATOR_ID:
                    // FALL THROUGH
                case self::CURATOR_EMAIL:
                    // FALL THROUGH
                case self::CURATOR_DISPLAY_NAME:
                    // FALL THROUGH
                case self::CURATOR_PASSWORD:
                    // FALL THROUGH
                case self::CURATOR_PUBLISH_BY_DEFAULT;
                    // FALL THROUGH
                case self::CURATOR_LEVEL_ID:
                    // FALL THROUGH
                case self::DOMAIN:
                    // FALL THROUGH
                case self::CURATOR_LEVEL_DESCRIPTION:
                    // FALL THROUGH
                case self::COMMUNITY_MESSAGE:
                    // FALL THROUGH
                case self::DISPLAY_COMMUNITY_MESSAGE:
                    // FALL THROUGH
                case self::CURRENT_APPLICATION_DIRECTORY:
                    // FALL THROUGH
                case self::HEADER_BACKGROUND_COLOR:
                    // FALL THROUGH
                case self::ENVIRONMENT_DISPLAY_NAME:
                    // FALL THROUGH
                case self::CURRENT_PHP_APPLICATION_WEB_ADDRESS:
                    // FALL THROUGH
                case self::CURRENT_PHP_APPLICATION_LOG_DIRECTORY:
                    // FALL THROUGH
                    return true;
                    break;

                default:
                    return false;
                    break;
            }
        }

        /**
         * Validates the variable name.
         *
         * @param string $name Session variable name.
         * @return bool Valid or No.
         * @throws {@link ControlExceptionConfigurationError}
         */
        protected static function _validateName($name)
        {
            if (self::validateName($name))
            {
                return true;
            }
            else
            {
                throw new ControlExceptionConfigurationError
                (
                    'Invalid ApplicationSession name \''
                    . $name
                    . '\'.'
                );
            }
        }

        /**
         * Verifies the application namespace is valid.
         *
         * @param string $nameSpace The application namespace.
         * @return bool valid or not
         * @throws {@link ControlExceptionConfigurationError}
         */
        protected static function _validateNameSpace($nameSpace)
        {
            switch ($nameSpace)
            {
                case self::APPLICATION_MEMEXPLEX:
                    return true;
                    break;

                default:
                    throw new ControlExceptionConfigurationError
                    (
                        'Invalid ApplicationSession namespace \''
                        . $nameSpace
                        . '\'.'
                    );
                    break;
            }
        }

    }
