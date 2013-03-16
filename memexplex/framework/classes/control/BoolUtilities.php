<?php
/**
 * Utilities for handling boolean variables.
 *
 * @package Framework
 * @subpackage Control
 * @author Adam Lyons
 * @author Ryan Somma
 */
class BoolUtilities
{

    /**
     * Accepts a variety of common strings as boolean true values, otherwise false.
     *
     * @param bool $value
     * @return string
     */
    public static function toBoolFlexible($value)
    {
        if (strtolower($value) == 'true'
            || strtolower($value) == 'y'
            || strtolower($value) == 'yes'
            || strtolower($value) == 'on'
            || strtolower($value) == 'aye'
            || strtolower($value) == 'male'
            || strtolower($value) == 'werd up'
            || $value == '1'
        )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Boolean coverted to string on or off
     *
     * @param bool $value
     * @return string
     */
    public static function toStringOnOff($value)
    {
        return ($value) ? 'on' :  'off';
    }

    /**
     * Boolean converted to string true or false
     *
     * @param bool $value
     * @return string
     */
    public static function toStringTrueFalse($value)
    {
        return ($value) ? 'true' :  'false';
    }

    /**
     * Converts a boolean value to whatever strings desired.
     *
     * @param bool $value
     * @param mixed $trueValue String for 1
     * @param mixed $falseValue String for 0
     * @return mixed True or False string value.
     */
    public static function toStringFlexible($value, $trueValue, $falseValue)
    {
        return ($value) ? $trueValue : $falseValue;
    }
}
