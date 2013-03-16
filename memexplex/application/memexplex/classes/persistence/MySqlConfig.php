<?php
/**
 * Configuration for accessing database. Username, DB name, password, etc.
 *
 * @package Framework
 * @subpackage Control
 * @author Ryan Somma
 */
class MySqlConfig
{

    public static function initialize()
    {
        if (!defined('DB_DATABASE'))
        {
            //database server
            define('DB_SERVER', "localhost");
            //database login name
            define('DB_USER', "DBUSER_HERE");
            //database login password
            define('DB_PASS', "DBPASSWORD_HERE");
            
            //database name
            define('DB_DATABASE', "memexplex_db");
            
            //smart to define your table names also
            define('TABLE_MEME', "meme");
            define('TABLE_TAG', "tag");
            define('TABLE_MEME_TAG', "meme_tag");
            define('TABLE_MEME_TRIPPLE', "meme_tripple");
            define('TABLE_MEMEPLEX', "memeplex");
        }
    }

}
