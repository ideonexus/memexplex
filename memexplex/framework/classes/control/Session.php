<?php
/**
 * Manages a session array so that it may be used similar
 * in fashion to a namespace.
 *
 * @package Framework
 * @subpackage Control
 * @todo Possibly set the number of seconds a SESSION variable exists.
 * @author Craig Avondo
 * @author Ryan Somma
 */
abstract class Session
{
    /**
     * Determines if a session variable under a namespace is set.
     *
     * @param string $nameSpace Session namespace.
     * @param string $name Session variable name.
     * @return bool Variable isset() or not.
     */
    final protected static function _sessionIsset($nameSpace, $name)
    {
        return isset($_SESSION[$nameSpace][$name]);
    }

    /**
     * Gets the value of a session variable under a namespace.
     *
     * @param string $nameSpace Session namespace.
     * @param string $name Session variable name.
     * @return mixed Session variable value.
     */
    final protected static function _sessionGet($nameSpace, $name)
    {
        return $_SESSION[$nameSpace][$name];
    }

    /**
     * Sets the value of a session variable under a namespace.
     *
     * @param string $nameSpace Session namespace.
     * @param string $name Session variable name.
     * @param mixed $value Session variable value.
     */
    final protected static function _sessionSet($nameSpace, $name, $value)
    {
        $_SESSION[$nameSpace][$name] = $value;
    }

    /**
     * Unsets the value of a session variable under a namespace.
     *
     * @param string $nameSpace Session namespace.
     * @param string $name Session variable name.
     */
    final protected static function _sessionUnset($nameSpace, $name)
    {
        unset($_SESSION[$nameSpace][$name]);
    }

    /**
     * Unsets a session namespace, killing all helpless variables therein.
     * BWA-HA-HA!!!
     *
     * @param string $nameSpace Session namespace.
     */
    final protected static function _sessionNamespaceUnset($nameSpace)
    {
        unset($_SESSION[$nameSpace]);
    }

}
