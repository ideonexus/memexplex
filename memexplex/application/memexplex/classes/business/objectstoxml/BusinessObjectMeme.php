<?php

/**
 * Converts meme and meme-related objects to SimpleXmlObjects.
 *
 * @package MemexPlex
 * @subpackage Business
 * @author Dr. Hans Reinhardt
 */

class BusinessObjectMeme
{

    /**
     * Converts a Meme object to a SimpleXmlObject.
     *
     * @param Meme $meme A meme to convert to simplexml
     * @return SimpleXmlObject
     */
    public static function _memeToXml(
        Meme $meme
    )
    {
        $xml = new SimpleXmlObject('<Meme/>');

        $xml->Id            = $meme->getId();
        $xml->Title         = $meme->getTitle();
        $xml->Text          = $meme->getText();
        $xml->Quote         = $meme->getQuote();
        $xml->ReferenceCount= $meme->getReferenceCount();
        $xml->TripleCount   = $meme->getTripleCount();
        $xml->SchemaCount   = $meme->getSchemaCount();
        $xml->Published     = $meme->getPublished();
        $xml->DatePublished = $meme->getDatePublished();

        if ($meme->getCurator())
        {
            $xml->appendChild(BusinessObjectCurator::_curatorToXml($meme->getCurator()));
        }

        if ($meme->getTaxonomyList())
        {
            $taxolist = $meme->getTaxonomyList();
            $taxolistxml = $xml->addChild('TaxonomyList');
            foreach ($taxolist as $item)
            {
                $taxolistxml->addChild('Taxonomy',$item->getText());
            }
        }
        
        return $xml;
    }

    /**
     * Converts a MemeList object to a SimpleXmlObject.
     *
     * @param MemeList $list A memelist to convert to simplexml
     * @return SimpleXmlObject
     */
    public static function _memeListToXml($list)
    {
        if ($list instanceof MemeList)
        {
            $xml = new SimpleXmlObject('<MemeList/>');

            foreach ($list as $item)
            {
                $xml->appendChild(self::_memeToXml($item));
            }

            $xml->TotalRows = $list->getTotalRows();
        }
        else
        {
            $xml = new SimpleXmlObject("<MemeList><Meme></Meme></MemeList>");
        }

        return $xml;
    }

    /**
     * Returns a MemeList filtered according to search parameters.
     *
     * @param MemexPlexObjectSearchParameters $searchParameters
     * @return SimpleXmlObject
     */
    public static function getMemeListXml(MemexPlexObjectSearchParameters $searchParameters)
    {
        $xml = null;
        $bc = new DataAccessComponentMeme();
        $list = $bc->getMemeList($searchParameters);
        return self::_memeListToXml($list);
    }

    /**
     * Returns a MemeList associated to a Reference.
     *
     * @param integer A reference database id.
     * @return SimpleXmlObject
     */
    public static function getMemeListByReferenceIdXml($referenceid)
    {
        $xml = null;
        $bc = new DataAccessComponentMeme();
        $list = $bc->getMemeListByReferenceId($referenceid);
        return self::_memeListToXml($list);
    }

    /**
     * Returns a MemeList associated to a Schema.
     *
     * @param integer A schema database id.
     * @return SimpleXmlObject
     */
    public static function getMemeListBySchemaIdXml($schemaid)
    {
        $xml = null;
        $bc = new DataAccessComponentMeme();
        $list = $bc->getMemeListBySchemaId($schemaid);
        return self::_memeListToXml($list);
    }
}
