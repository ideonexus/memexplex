<?php

/**
 * The time class returns a PHP DateTime object and encapsulates
 * functionality for converting dates.
 *
 * @package Framework
 * @subpackage Control
 * @author Ryan Somma
 */
class Time
{

    /**
     * Returns the current GMT date in a date textfield input format.
     *
     * @return string <todo:description>
     */
    public static function getZuluDateFormFormat()
    {
        //        $javaScriptDate = new DateTime(gmdate('M d Y H:i:s'));
        //        return $javaScriptDate->format('m/j/Y');
        return gmdate('m/d/Y');
    }

    /**
     * Converts a date and time to military format.
     *
     * @param string datetime
     * @param boolean GMT flag.
     * @return string dateime
     */
    public static function getMilitaryDateTime($dateTime='',$zulu=true)
    {
        //        $zuluDateTime = new DateTime(gmdate('M d Y H:i:s'));
        //        return $zuluDateTime->format('j M Y / Hi') . ' Z';
        if
        (
            $dateTime == ''
            && $zulu
        )
        {
            return strtoupper(gmdate('d M Y / Hi')) . ' Z';
        }
        else if
        (
            $dateTime == ''
            && !$zulu
        )
        {
            return 'Placeholder for Local Date';
        }
        else
        {
            return strtoupper(date('d M Y / Hi',mktime($dateTime))) . ' Z';
        }
    }

    /**
     * Compares two dates.
     *
     * returns < 0 if a is less than b
     * returns > 0 if a is greater than b
     * returns = 0 if they are equal.
     *
     * Note: E_STRICT warning is being suppressed.
     *
     * @param string $a <todo:description>
     * @param string $b <todo:description>
     * @return int
     */
    public static function compareDates
    (
        $a,
        $b
    )
    {
        $compare = 0;
        $date1 = @strtotime($a);
        $date2 = @strtotime($b);
        if ($date1 != $date2)
        {
            $compare = ($date1 < $date2) ? -1 : 1;
        }
        return $compare;
    }

}
