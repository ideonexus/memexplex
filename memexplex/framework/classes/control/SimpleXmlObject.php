<?php
/**
 * @package Framework
 * @subpackage Control
 */
class SimpleXmlObject extends SimpleXMLElement
{

    /**
     * Returns a string representation for intances of this class.
     *
     * @return string
     */
    public function serialize()
    {
        return $this->asXML();
    }

    /**
     * Constructs an instance of this class from a string representation.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        parent::__construct($serialized);
    }

    /**
     * @param SimpleXmlObject $child
     */
    public function appendChild(SimpleXmlObject $child)
    {
        $parent = dom_import_simplexml($this);
        $node = dom_import_simplexml($child);
        $node = $parent->ownerDocument->importNode($node, true);
        $parent->appendChild($node);
    }

    /**
     * @param SimpleXmlObject $child
     */
    public function deleteChild(SimpleXmlObject $child)
    {
        $node = dom_import_simplexml($child);
        $node->parentNode->removeChild($node);
    }

    /**
     * Convenience function for SimpleXmlObject->xpath()
     *
     * @param string $xpath
     * @param int $index [optional]
     * @return mixed <p>
     * returns the first matching SimpleXmlObject
     * unless index is non-zero, otherwise returns false.
     * </p>
     */
    public function selectItem
    (
        $xpath,
        $index = 0
    )
    {
        if ($xpath != '')
        {
            $array = $this->xpath($xpath);
            if ($array)
            {
                return $array[$index];
            }
        }

        return false;
    }

    /**
     * Interprets an XML file into an object
     * @param filename string <p>
     * Path to the XML file
     * </p>
     * @param options int[optional] <p>
     * Since PHP 5.1.0 and Libxml 2.6.0, you may also use the
     * options parameter to specify additional Libxml parameters.
     * </p>
     * @param ns string[optional] <p>
     * </p>
     * @param is_prefix bool[optional] <p>
     * </p>
     * @return SimpleXmlObject an object of class SimpleXmlObject with
     * properties containing the data held within the XML document. On errors, it
     * will return false.
     */
    public static function loadFile
    (
        $filename,
        $options = null,
        $ns = null,
        $is_prefix = null
    )
    {
        switch (func_num_args())
        {
            case 1:
                return simplexml_load_file($filename, __CLASS__);
            case 2:
                return simplexml_load_file($filename, __CLASS__, $options);
            case 3:
                return simplexml_load_file($filename, __CLASS__, $options, $ns);
            default:
                return simplexml_load_file($filename, __CLASS__, $options, $ns, $is_prefix);
        }
    }

    /**
     * Interprets a string of XML into an object
     * @param data string <p>
     * A well-formed XML string
     * </p>
     * @param options int[optional] <p>
     * Since PHP 5.1.0 and Libxml 2.6.0, you may also use the
     * options parameter to specify additional Libxml parameters.
     * </p>
     * @param ns string[optional] <p>
     * </p>
     * @param is_prefix bool[optional] <p>
     * </p>
     * @return SimpleXmlObject an object of class SimpleXmlObject with
     * properties containing the data held within the xml document. On errors, it
     * will return false.
     */
    public static function loadString
    (
        $data,
        $options = null,
        $ns = null,
        $is_prefix = null
    )
    {
        switch (func_num_args())
        {
            case 1:
                return simplexml_load_string($data, __CLASS__);
            case 2:
                return simplexml_load_string($data, __CLASS__, $options);
            case 3:
                return simplexml_load_string($data, __CLASS__, $options, $ns);
            default:
                return simplexml_load_string($data, __CLASS__, $options, $ns, $is_prefix);
        }
    }

    /**
     * Get a <literal>SimpleXmlObject</literal> object from a DOM node.
     * @param node DOMNode <p>
     * A DOM Element node
     * </p>
     * @return SimpleXmlObject a SimpleXmlObject or false on failure.
     */
    public static function importDom(DOMNode $node)
    {
        return simplexml_import_dom($node, __CLASS__);
    }

}
