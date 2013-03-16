<?php
/**
 * This class manages constants for the application.
 *
 * @package Framework
 * @subpackage Control
 * @author Ryan Somma
 */
class Constants
{

    /**
     * @var array List of valid constants.
     */
    protected static $validConstants = array
    (
         'CURRENT_APPLICATION'
        ,'CURRENT_PAGE_CODE'
        ,'AJAX_METHOD'
    );

    /**
     * Sets a constant value.
     *
     * @param string $name Name of constant.
     * @param mixed $value Value of constant.
     */
    public static function setConstant($name, $value = '')
    {
        if (self::validateName($name))
        {
            if (!defined($name))
            {
                define($name, $value);
            }
        }
    }

    /**
     * Gets a constant value.
     *
     * @param string $name Name of constant.
     * @return mixed Value of constant.
     */
    public static function getConstant($name)
    {
        if (self::validateName($name))
        {
            if (defined($name))
            {
                return constant($name);
            }
        }
    }

    /**
     * Validates the name of a constant against the
     * array of ValidConstants.
     *
     * @param string $name Name of constant.
     * @return bool Valid or no.
     * @throws {@link ControlExceptionConfigurationError}
     */
    private static function validateName($name)
    {
        if (!in_array($name, self::$validConstants))
        {
            throw new ControlExceptionConfigurationError
            (
                'A valid name is required for Constants.'
            );
        }

        return true;
    }

}
