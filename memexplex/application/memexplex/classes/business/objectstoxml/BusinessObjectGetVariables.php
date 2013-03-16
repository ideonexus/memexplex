<?php
/**
 * Functions for GET variables and SimpleXml.
 *
 * @package MemexPlex
 * @subpackage Business
 * @author Professor Hubert J. Farnsworth
 */

class BusinessObjectGetVariables
{

    /**
     * Takes all the $_GET variables currently accessible to the application and
     * converts them to a SimpleXmlObject format.
     *
     * @return SimpleXmlObject Get variables listed under a <GetVariables> xml node.
     */
    public static function getVariablesToXml()
    {
        $xml = new SimpleXmlObject('<GetVariables/>');
        foreach($_GET as $key=>$value)
        {
            $xml->addChild($key, $value);
        }

        return $xml;
    }

}
