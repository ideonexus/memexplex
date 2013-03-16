<?php

/**
 *
 * Workaround for the fact that PHP does not allow sessionalizing internal objects.
 *
 * Session save handling of SimpleXMLElement objects (applies to/ tested with PHP 5.1.5 and PHP 5.2.1).
 *
 * The myClass pattern allows for conveniently accessing XML structures while being session safe.
 *
 * @package Framework
 * @subpackage Persistence
 * @author Ryan Somma, Adam Lyons
 * @link https://students.kiv.zcu.cz/doc/php5/manual/cs/ref.simplexml.php.html
 */

class SimpleXMLSessioned
{

    /**
     * @var SimpleXMLObject
     */
    private $xmlObject = null;

    /**
     * @var string
     */
    private $xmlString = '';

    /**
     * Loads file into SimpleXMLObject.
     *
     * @param string $xmlFile <todo:description>
     */
    public function __construct($xmlFile)
    {
        if (file_exists($xmlFile))
        {
            $this->xmlObject = simplexml_load_file($xmlFile);
            $this->xmlString = (string) $this->xmlObject->asXML();
        }
        else
        {
            throw new ControlException
            (
                'File ' . $xmlFile . ' not found.'
            );
        }
    }

    /**
     * Converts SimpleXMLObject to string and unsets it
     */
    public function __destruct()
    {
        $this->xmlString = (string) $this->xmlObject->asXML();
        unset($this->xmlObject); // this object would otherwise crash on
                                 // the subsequent call of session_start()!
    }

    /**
     * Xml string to session.
     *
     * @return array <todo:description>
     */
    public function __sleep()
    {
        return array('xmlString');
    }

    /**
     * Xml string session to SimpleXMLObject.
     *
     */
    public function __wakeup()
    {
        $this->xmlObject = simplexml_load_string($this->xmlString);
    }

    /**
     * @return SimpleXMLObject
     */
    public function getSimpleXMLObject()
    {
        return $this->xmlObject;
    }

}
