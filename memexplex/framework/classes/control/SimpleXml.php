<?php

/**
 * Utilities for SimpleXmlElements
 *
 * @package Framework
 * @subpackage Control
 * @author Ryan Somma
 */

class SimpleXml
{
    /**
     * Gets simplexmlelement nodes based on xpath
     *
     * @param object $simpleXmlObject <todo:description>
     * @param string $xPath <todo:description>
     * @param int $index <todo:description>
     * @return mixed <todo:description>
     */
    public static function getSimpleXmlItem
    (
        SimpleXMLElement $simpleXmlObject,
        $xPath = '',
        $index = 0
    )
    {
        if ($xPath != '')
        {
            $tempArray = $simpleXmlObject->xpath($xPath);
            if ((bool)$tempArray)
            {
                return $tempArray[$index];
            }
        }

        return false;
    }

    /**
     * Appends nodes to SimpleXmlElements.
     *
     * @param SimpleXMLElement $parent <todo:description>
     * @param SimpleXMLElement $newChild <todo:description>
     */
    public static function simpleXmlAppend
    (
        SimpleXMLElement $parent,
        SimpleXMLElement $child
    )
    {
        $node1 = dom_import_simplexml($parent);
        $domSxe = dom_import_simplexml($child);
        $node2 = $node1->ownerDocument->importNode($domSxe, true);
        $node1->appendChild($node2);
    }

    /**
     * Deletes nodes from SimpleXml element.
     *
     * @param SimpleXMLElement $parent <todo:description>
     * @param SimpleXMLElement $newChild <todo:description>
     */
    public static function simpleXmlDelete
    (
        SimpleXMLElement $simpleXmlData,
        $xpath
    )
    {
        list($theNodeToBeDeleted) = $simpleXmlData->xpath($xpath);
        $oNode = dom_import_simplexml($theNodeToBeDeleted);
        $oNode->parentNode->removeChild($oNode);
    }

}
