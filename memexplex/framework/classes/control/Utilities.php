<?php
/**
 * Various useful utilities for developers.
 *
 * @package Framework
 * @subpackage Control
 * @author Craig Avondo
 */
class Utilities {

    /**
     * <todo:description>
     *
     * @param string $filename <todo:description>
     * @param string $buffer <todo:description>
     */
    public static function writeToFile
    (
        $filename,
        $buffer
    )
    {
        $fp = fopen($filename, 'w');
        if ($fp)
        {
            fputs($fp, $buffer);
            fclose($fp);
        }
    }

    /**
     * <todo:description>
     *
     * @param string $xml <todo:description>
     * @return string <todo:description>
     */
    public static function tidyXml($xml)
    {
        $dom = new DOMDocument();

        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xml);

        return $dom->saveXML();
    }

    /**
     * <todo:description>
     *
     * @param string $xml <todo:description>
     * @param bool $pretty_format <todo:description>
     */
    public static function echoXml
    (
        $xml,
        $pretty_format = true
    )
    {
        if (!$xml)
        {
            return;
        }

        if ($pretty_format === true)
        {
            $xml = self::tidyXml($xml);
        }

        echo '<pre><br /><br />'
           . htmlentities($xml)
           . '</pre>';
    }

    /**
     * <todo:description>
     *
     * @param mixed $var <todo:description>
     * @param mixed $label <todo:description>
     */
    public static function echoVar
    (
        $var,
        $label = ''
    )
    {
        if (!isset($var))
        {
            return;
        }

        echo '<pre>'
           . $label
           . '<br /><br />'
           . print_r($var, true)
           . '</pre>';
    }

}
