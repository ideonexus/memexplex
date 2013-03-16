<?php

/**
 * Converts Schema and Schema-related objects to SimpleXmlObjects.
 *
 * @package MemexPlex
 * @subpackage Business
 * @author The Brain
 */

class BusinessObjectSchema
{

    /**
     * Converts a Schema object to a SimpleXmlObject.
     *
     * @param Schema $schema
     * @return SimpleXmlObject
     */
    public static function _schemaToXml
    (
        Schema $schema
    )
    {
        $xml = new SimpleXmlObject('<Schema/>');

        $xml->Id            = $schema->getId();
        $xml->Title         = $schema->getTitle();
        $xml->Description   = $schema->getDescription();
        $xml->Published     = $schema->getPublished();
        $xml->DatePublished = $schema->getDatePublished();
        $xml->MemeCount     = $schema->getMemeCount();
        
        if ($schema->getCurator())
        {
            $xml->appendChild(BusinessObjectCurator::_curatorToXml($schema->getCurator()));
        }
        
        if ($schema->getMemeList())
        {
            $xml->appendChild(BusinessObjectMeme::_memeListToXml($schema->getMemeList()));
        }

        $taxolist = $schema->getTaxonomyList();
        $taxolistxml = $xml->addChild('TaxonomyList');
        foreach ($taxolist as $item)
        {
            $taxolistxml->addChild('Taxonomy',$item->getText());
        }

    /**
     * [TODO: Child SchemaList should be populated here, currently application
     * does this in the PageObjects class.]
     */
        /*
        $list = $schema->getSchemaList();
        $tempxml = null;
        foreach ($list as $item)
        {
            $tempxml .= self::_schemaListToXml($item);
        }
        $xml .= Xml::createParentNodeXml
        (
            'SchemaList'
            ,$tempxml
        );
		*/

        return $xml;
    }

    /**
     * Converts a SchemaList object to a SimpleXmlObject.
     *
     * @param SchemaList $list
     * @return SimpleXmlObject
     */
    public static function _schemaListToXml($list=null)
    {
        if ($list instanceof SchemaList)
        {
            $xml = new SimpleXmlObject('<SchemaList/>');

            foreach ($list as $item)
            {
                $xml->appendChild(self::_schemaToXml($item));
            }

            $xml->TotalRows = $list->getTotalRows();
        }
        else
        {
            $xml = new SimpleXmlObject("<SchemaList><Schema></Schema></SchemaList>");
        }

        return $xml;
    }

    /**
     * Returns a SchemaList based on submitted search parameters.
     *
     * @param MemexPlexObjectSearchParameters $searchParameters
     * @return SimpleXmlObject
     */
    public static function getSchemaListXml(MemexPlexObjectSearchParameters $searchParameters)
    {
        $xml = null;
        $bc = new DataAccessComponentSchema;
        $list = $bc->getSchemaList($searchParameters);
        return self::_schemaListToXml($list);
    }

    /**
     * Returns a List of Child Schemas to a Schema.
     *
     * @param integer $schemaid Database id of the Schema.
     * @return SimpleXmlObject
     */
    public static function getSchemaListByParentSchemaIdXml($schemaid)
    {
        $xml = null;
        $bc = new DataAccessComponentSchema();
        $list = $bc->getSchemaListByParentSchemaId($schemaid);
        return self::_schemaListToXml($list);
    }

    /**
     * Returns a List of Parent Schemas to a Schema.
     *
     * @param integer $schemaid Database id of the Schema.
     * @return SimpleXmlObject
     */
    public static function getSchemaListByChildSchemaIdXml($schemaid,$publishedOnly=false)
    {
        $xml = null;
        $bc = new DataAccessComponentSchema();
        $list = $bc->getSchemaListByChildSchemaId($schemaid,$publishedOnly);
        return self::_schemaListToXml($list);
    }

    /**
     * Returns a List of Schemas making use of a Meme.
     *
     * @param integer $memeid Database id of the Meme.
     * @return SimpleXmlObject
     */
    public static function getSchemaListByMemeIdXml($memeid)
    {
        $xml = null;
        $bc = new DataAccessComponentSchema();
        $list = $bc->getSchemaListByMemeId($memeid);
        return self::_schemaListToXml($list);
    }

}
