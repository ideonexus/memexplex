<?php
/**
 * URL utilities.
 *
 * @package Framework
 * @subpackage Control
 * @link http://dev.kanngard.net/Permalinks/ID_20050507183447.html
 */
class Url
{

    /**
     * Gets a url of current context. Useful in debugging AJAX calls.
     *
     * @return string Formatted URL.
     */
    public static function selfURL()
    {

        $s = empty($_SERVER['HTTPS'])
           ? ''
           : ($_SERVER['HTTPS'] == 'on') ? 's' : '';

        $protocol =
            substr
            (
                strtolower($_SERVER['SERVER_PROTOCOL']),
                0,
                strpos(strtolower($_SERVER['SERVER_PROTOCOL']), '/')
            ) . $s;

        $port = ($_SERVER['SERVER_PORT'] == '80')
              ? ''
              : (':'.$_SERVER['SERVER_PORT']);

        return $protocol . '://' . $_SERVER['SERVER_NAME']
                         . $port . $_SERVER['REQUEST_URI'];
    }

}
