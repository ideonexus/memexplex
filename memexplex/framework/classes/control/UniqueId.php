<?php

/**
 * Generates a unique id for use as a key in a log file. 
 *
 * @package Framework
 * @subpackage Control
 * @author Adam Lyons
 */
class UniqueId
{

    /**
     * <todo:description>
     *
     * @return string <todo:description>
     */
    public static function getUniqueId()
    {
        return md5(uniqid(rand(), true));
    }

}
