<?php
/**
 * Handles sessions for pages, each page acts as a namespace.
 *
 * @package Framework
 * @subpackage Control
 * @author Craig Avondo
 * @author Ryan Somma
 */
class PageSession extends Session
{

    /**
     * @var string Current page.
     */
    protected static $currentNameSpace;

    /**
     * Sets the namespace.
     *
     * @param string $nameSpace The page namespace.
     */
    public static function initialize($nameSpace = CURRENT_PAGE_CODE)
    {
        if (self::_validateNameSpace($nameSpace))
        {
            self::$currentNameSpace = $nameSpace;
        }
    }

    /**
     * Returns a session value.
     *
     * @param string $name Variable Name.
     * @param string $nameSpace Pagecode.
     * @return mixed Variable value.
     */
    public static function getValue($name, $nameSpace = '')
    {
        if ($nameSpace == '')
        {
            $nameSpace = self::$currentNameSpace;
        }

        if (self::_validateName($name) &&
            parent::_sessionIsset($nameSpace, $name))
        {
            return parent::_sessionGet($nameSpace, $name);
        }
    }

    /**
     * Sets a session value.
     *
     * @param string $name Variable name.
     * @param mixed $value Variable value.
     * @param string $nameSpace Page code.
     */
    public static function setValue($name, $value, $nameSpace = '')
    {
        if ($nameSpace == '')
        {
            $nameSpace = self::$currentNameSpace;
        }

        if (self::_validateName($name))
        {
            parent::_sessionSet($nameSpace, $name, $value);
        }
    }

    /**
     * Checks if a session variable is set.
     *
     * @param string $name Variable name.
     * @param string $nameSpace Page code.
     * @return bool isset
     */
    public static function isNameSet($name, $nameSpace='')
    {
        if ($nameSpace == '')
        {
            $nameSpace = self::$currentNameSpace;
        }

        return parent::_sessionIsset($nameSpace, $name);
    }

    /**
     * Unsets a session variable.
     *
     * @param string $name Variable name.
     * @param string $nameSpace Page code.
     */
    public static function unSetName($name, $nameSpace = '')
    {
        if ($nameSpace == '')
        {
            $nameSpace = self::$currentNameSpace;
        }

        if (self::_validateName($name))
        {
            parent::_sessionUnset($nameSpace, $name);
        }
    }

    /**
     * Unsets all session variables for a page.
     *
     * @param string $nameSpace
     */
    public function unsetAll($nameSpace = '')
    {
        if ($nameSpace == '')
        {
            $nameSpace = self::$currentNameSpace;
        }

        parent::_sessionNamespaceUnset($nameSpace);
    }

    /**
     * Validates the variable name. Variable names are required.
     *
     * @param string $name Namespace name.
     * @return bool isset
     * @throws {@link ControlExceptionConfigurationError}
     */
    protected static function _validateName($name)
    {
        if ($name != '')
        {
            return true;
        }
        else
        {
            throw new ControlExceptionConfigurationError
            (
                'A name is required for PageSession variables.'
            );
        }
    }

    /**
     * Validates the namespace. Namespaces are required.
     *
     * @param string $nameSpace Namespace name.
     * @return bool isset
     * @throws {@link ControlExceptionConfigurationError}
     */
    protected static function _validateNameSpace($nameSpace)
    {
        if ($nameSpace != '')
        {
            return true;
        }
        else
        {
            throw new ControlExceptionConfigurationError
            (
                'A namespace is required for PageSession variables.'
            );
        }
    }

}
