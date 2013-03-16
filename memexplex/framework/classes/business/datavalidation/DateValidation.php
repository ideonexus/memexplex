<?php
/**
 * Functions for validating dates submitted to a form.
 *
 * @package Framework
 * @subpackage Business
 * @author Ryan Somma
 */
class DateValidation
{

    /**
     * Checks if a date is future GMT.
     *
     * @param string $date Date value.
     * @param string $label Form element HTML Id.
     * @return array Pass/Fail, Form element label.
     */
    public static function checkFutureZuluDate($date, $label = '')
    {
        $submittedZuluDateTime = $date;
        $presentZuluDateTime = Time::getZuluDateFormFormat();

        if (strtotime($submittedZuluDateTime) > strtotime($presentZuluDateTime))
        {
            return array(false, $label . ' cannot be a future date.');
        }

        return array(true);
    }

    /**
     * Checks for a properly formatted date value.
     *
     * @param string $date Date value.
     * @param string $label Form element HTML Id.
     * @return array Pass/Fail, Form element label.
     */
    public static function checkDate($date, $label = '')
    {
        if ($date != '')
        {
            if(preg_match("/[0-9]{1,2}\\/[0-9]{1,2}\\/[0-9]{4}/",$date))
            {
                /* 1. Extract the numeric data presented as MM/DD/YYYY */
                list($MM, $DD, $YYYY) = explode("/", $date);

                /* 2. Validate the date */
                if(!checkdate($MM, $DD, $YYYY))
                {
                    return array(false, 'Please enter a valid ' . $label . '.');
                }
            }
            else
            {
                return array(false, 'Please enter a valid ' . $label . '.');
            }
        }

        return array(true);
    }

    /**
     * Validates a date in a required field.
     *
     * @param string $date Date value.
     * @param string $label Form element HTML Id.
     * @return array Pass/Fail, Form element label.
     */
    public static function checkRequiredDate($date = '', $label = '')
    {
        if ($date == '')
        {
            return array(false, 'A ' . $label . ' is required.');
        }

        return self::checkDate($date, $label);
    }
}