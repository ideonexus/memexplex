<?php
/**
 * Converts curator and curator-related objects to SimpleXmlObjects.
 *
 * @package MemexPlex
 * @subpackage Business
 * @author Motoko Kusanagi
 */

class BusinessObjectCurator
{

    /**
     * Converts a Curator object to a SimpleXmlObject.
     *
     * @param Curator $curator Curator object to be converted.
     * @return SimpleXmlObject Curator as PHP SimpleXml object.
     */
    public static function _curatorToXml(
        Curator $curator
    )
    {
        $xml                   = new SimpleXmlObject('<Curator/>');
        $xml->Id               = $curator->getId();
        $xml->Email            = $curator->getEmail();
        $xml->DisplayName      = $curator->getDisplayName();
        $xml->PublishByDefault = $curator->getPublishByDefault();

        $level = $curator->getLevel();

        $levelxml              = $xml->addChild('<CuratorLevel/>');
        $levelxml->Id          = $level->getId();
        $levelxml->Code        = $level->getCode();
        $levelxml->Description = $level->getDescription();

        return $xml;
    }

    /**
     * Returns a MemeList filtered according to search parameters.
     *
     * @param MemexPlexObjectSearchParameters $searchParameters
     * @return SimpleXmlObject
     */
    public static function getCuratorXml($email)
    {
        $xml = null;
        $bc = new DataAccessComponentCurator();
        $list = $bc->getCurator($email);
        return self::_curatorToXml($list);
    }

}
