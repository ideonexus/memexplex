<?php
/**
 * Utility class filled with static functions for arrays.
 *
 * @package Framework
 * @subpackage Control
 * @author Ryan Somma
 */
class ArrayUtilities
{

    /**
     * Searches a multidimensional array for an item.
     *
     * @param string $needle What's being searched for.
     * @param string $haystack The multi-dimensional array being searched.
     * @return bool Is the needle in the haystack?
     */
    public static function in_multi_array($needle, $haystack)
    {
        $in_multi_array = false;
        if(in_array($needle, $haystack))
        {
            $in_multi_array = true;
        }
        else
        {
            for($i = 0; $i < sizeof($haystack); $i++)
            {
                if(is_array($haystack[$i]))
                {
                    if(self::in_multi_array($needle, $haystack[$i]))
                    {
                        $in_multi_array = true;
                        break;
                    }
                }
            }
        }
        return $in_multi_array;
    }

}
