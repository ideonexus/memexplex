<?php
/**
 * <todo:description>
 *
 * @package Framework
 * @subpackage Control
 * @author Craig Avondo
 */
class Xml
{

    /**
     * <todo:description>
     *
     * @param string $name <todo:description>
     * @param mixed $value optional <todo:description>
     * @return string <todo:description>
     */
    public static function createAttributeXml
    (
        $name,
        $value
    )
    {
        $xml = null;

        $xml .= $name;
        $xml .= '="';
        $xml .= htmlspecialchars($value);
        $xml .= '"';

        return $xml;
    }

    /**
     * <todo:description>
     *
     * @param string $name <todo:description>
     * @param string $value <todo:description>
     * @return string <todo:description>
     */
    public static function createTextNodeXml
    (
        $name,
        $text,
        array $attributes = array()
    )
    {
        $xml = null;

        $xml .= '<';
        $xml .= $name;
        foreach ($attributes as $name => $value)
        {
            $xml .= ' ';
            $xml .= self::createAttribute($name, $value);
        }
        $xml .= '>';
        $xml .= htmlspecialchars($text);
        $xml .= '</';
        $xml .= $name;
        $xml .= '>';

        return $xml;
    }

    /**
     * <todo:description>
     *
     * @param string $type <todo:description>
     * @param string $text <todo:description>
     * @param array $attributes <todo:description>
     * @return string <todo:description>
     */
    public static function createParentNodeXml
    (
        $name,
        $childrenXml,
        array $attributes = array()
    )
    {
        $xml = null;

        $xml .= '<';
        $xml .= $name;
        foreach ($attributes as $name => $value)
        {
            $xml .= ' ';
            $xml .= self::createAttribute($name, $value);
        }
        $xml .= '>';
        $xml .= $childrenXml;
        $xml .= '</';
        $xml .= $name;
        $xml .= '>';

        return $xml;
    }

}
