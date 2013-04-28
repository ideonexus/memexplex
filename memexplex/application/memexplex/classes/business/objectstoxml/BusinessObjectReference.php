<?php

/**
 * Converts reference and reference-related objects to SimpleXmlObjects.
 *
 * @package MemexPlex
 * @subpackage Business
 * @author Lex Luthor
 */

class BusinessObjectReference
{

    /**
     * Converts a Reference object to a SimpleXmlObject.
     *
     * @param Reference $reference
     * @return SimpleXmlObject
     */
    public static function _referenceToXml
    (
        Reference $reference
    )
    {
        $referenceSuperType = $reference->getReferenceSuperType();
        $referenceType      = $reference->getReferenceType();

        $xml = new SimpleXmlObject('<Reference/>');

        $xml->Id                            = $reference->getId();
	    	$xml->ReferenceSuperTypeId          = $referenceSuperType->getId();
	    	$xml->ReferenceSuperTypeDescription = $referenceSuperType->getDescription();
	    	$xml->ReferenceTypeId               = $referenceType->getId();
	    	$xml->ReferenceTypeDescription      = $referenceType->getDescription();
        $xml->ReferenceDate                 = $reference->getReferenceDate();
        $xml->Title                         = $reference->getTitle();
        $xml->PublicationLocation           = $reference->getPublicationLocation();
        $xml->PublisherPeriodical           = $reference->getPublisherPeriodical();
        $xml->VolumePages                   = $reference->getVolumePages();
        $xml->Url                           = $reference->getUrl();
        $xml->ReferenceService              = $reference->getReferenceService();
        $xml->DateRetrieved                 = $reference->getDateRetrieved();
        $xml->ISBN                          = $reference->getIsbn();
        $xml->EAN                           = $reference->getEan();
        $xml->UPC                           = $reference->getUpc();
        $xml->SmallImageUrl                 = $reference->getSmallImageUrl();
        $xml->LargeImageUrl                 = $reference->getLargeImageUrl();
        $xml->ASIN                          = $reference->getAsin();
        $xml->AmazonUrl                     = $reference->getAmazonUrl();
        $xml->Published                     = $reference->getPublished();
        $xml->DatePublished                 = $reference->getDatePublished();
        $xml->MemeCount                     = $reference->getMemeCount();
        $xml->ReferenceCount                = $reference->getReferenceCount();

        if ($reference->getCurator())
        {
            $xml->appendChild(BusinessObjectCurator::_curatorToXml($reference->getCurator()));
        }

        $authlist = $reference->getAuthors();
        $authlistxml = $xml->addChild('Authors');
        foreach ($authlist as $item)
        {
            $authorxml = new SimpleXmlObject('<Author/>');
            $authorxml->Id        = $item->getId();
            $authorxml->FullName  = $item->getFullName();
            $authorxml->FirstName = $item->getFirstName();
            $authorxml->LastName  = $item->getLastName();
            $authlistxml->appendChild($authorxml);
        }

        $taxolist = $reference->getTaxonomyList();
        $taxolistxml = $xml->addChild('TaxonomyList');
        foreach ($taxolist as $item)
        {
            $taxolistxml->addChild('Taxonomy',$item->getText());
        }

        return $xml;
    }

    /**
     * Converts a ReferenceList object to a SimpleXmlObject.
     *
     * @param ReferenceList $list
     * @return SimpleXmlObject
     */
    public static function _referenceListToXml($list)
    {
        if ($list instanceof ReferenceList)
        {
            $xml = new SimpleXmlObject('<ReferenceList/>');

            foreach ($list as $item)
            {
                $xml->appendChild(self::_referenceToXml($item));
            }

            $xml->TotalRows = $list->getTotalRows();
        }
        else
        {
            $xml = new SimpleXmlObject("<ReferenceList><Reference></Reference></ReferenceList>");
        }
        return $xml;
    }

    /**
     * Converts a ReferenceSuperType object to a SimpleXmlObject.
     *
     * @param ReferenceSuperType $referenceSuperType
     * @return SimpleXmlObject
     */
    public static function _referenceSuperTypeToXml(
        ReferenceSuperType $referenceSuperType
    )
    {
        $xml = new SimpleXmlObject('<ReferenceSuperType/>');

    	$xml->ReferenceSuperTypeId          = $referenceSuperType->getId();
	    $xml->ReferenceSuperTypeDescription = $referenceSuperType->getDescription();

        return $xml;
    }

    /**
     * Converts a ReferenceSuperType object to a SimpleXmlObject.
     *
     * @param ReferenceSuperTypeList $list
     * @return SimpleXmlObject
     */
    public static function _referenceSuperTypeListToXml($list)
    {
        if ($list instanceof ReferenceSuperTypeList)
        {
            $xml = new SimpleXmlObject('<ReferenceSuperTypeList/>');

            foreach ($list as $item)
            {
                $xml->appendChild(self::_referenceSuperTypeToXml($item));
            }
        }
        else
        {
            $xml = new SimpleXmlObject("<ReferenceSuperTypeList><ReferenceSuperType></ReferenceSuperType></ReferenceSuperTypeList>");
        }
        return $xml;
    }

    /**
     * Converts a ReferenceType object to a SimpleXmlObject.
     *
     * @param ReferenceType $referenceType
     * @return SimpleXmlObject
     */
    public static function _referenceTypeToXml(
        ReferenceType $referenceType
    )
    {
        $xml = new SimpleXmlObject('<ReferenceType/>');

    	$xml->ReferenceTypeId          = $referenceType->getId();
    	$xml->ReferenceSuperTypeId     = $referenceType->getReferenceSuperTypeId();
    	$xml->ReferenceTypeDescription = $referenceType->getDescription();

        return $xml;
    }

    /**
     * Converts a ReferenceTypeList object to a SimpleXmlObject.
     *
     * @param ReferenceTypeList $list
     * @return SimpleXmlObject
     */
    public static function _referenceTypeListToXml($list)
    {
        if ($list instanceof ReferenceTypeList)
        {
            $xml = new SimpleXmlObject('<ReferenceTypeList/>');

            foreach ($list as $item)
            {
                $xml->appendChild(self::_referenceTypeToXml($item));
            }
        }
        else
        {
            $xml = new SimpleXmlObject("<ReferenceTypeList><ReferenceType></ReferenceType></ReferenceTypeList>");
        }
        return $xml;
    }

    /**
     * Returns a ReferenceList based on submitted search parameters.
     *
     * @param MemexPlexObjectSearchParameters $searchParameters
     * @return SimpleXmlObject
     */
    public static function getReferenceListXml(MemexPlexObjectSearchParameters $searchParameters)
    {
        $xml = null;
        $bc = new DataAccessComponentReference();
        $list = $bc->getReferenceList($searchParameters);
        return self::_referenceListToXml($list);
    }

    /**
     * Returns a list of References associated to a Meme. Should be only one.
     *
     * @param integer $memeid Meme database id.
     * @return SimpleXmlObject
     */
    public static function getReferenceByMemeIdXml($memeid)
    {
        $xml = null;
        $bc = new DataAccessComponentReference();
        $refid = $bc->getReferenceIdByMemeId($memeid);
        $list = null;
        if ($refid)
        {
            $searchParameters = new MemexPlexObjectSearchParameters();
            $searchParameters->setId($refid);
            $list = $bc->getReferenceList($searchParameters);
        }
        return self::_referenceListToXml($list);
    }

    /**
     * Returns a list of child References to a Reference.
     *
     * @param integer $referenceid Reference database id.
     * @return SimpleXmlObject
     */
    public static function getReferenceListBySchemaIdXml($schemaid)
    {
        $xml = null;
        $bc = new DataAccessComponentReference();
        $list = $bc->getReferenceListBySchemaId($schemaid);
        return self::_referenceListToXml($list);
    }

    /**
     * Returns a list of child References to a Reference.
     *
     * @param integer $referenceid Reference database id.
     * @return SimpleXmlObject
     */
    public static function getReferenceListByParentReferenceIdXml($referenceid)
    {
        $xml = null;
        $bc = new DataAccessComponentReference();
        $list = $bc->getReferenceListByParentReferenceId($referenceid);
        return self::_referenceListToXml($list);
    }

    /**
     * Returns a list of parent References (should be only one) to a Reference.
     *
     * @param integer $referenceid Reference database id.
     * @return SimpleXmlObject
     */
    public static function getReferenceListByChildReferenceIdXml($referenceid)
    {
        $xml = null;
        $bc = new DataAccessComponentReference();
        $list = $bc->getReferenceListByChildReferenceId($referenceid);
        return self::_referenceListToXml($list);
    }

    /**
     * Returns a list of all ReferenceSuperTypes.
     *
     * @return SimpleXmlObject
     */
    public static function getReferenceSuperTypeListXml()
    {
        $xml = null;
        $bc = new DataAccessComponentReference();
        $list = $bc->getReferenceSuperTypeList();
        return self::_referenceSuperTypeListToXml($list);
    }

    /**
     * Returns a list of all ReferenceTypes.
     *
     * @return SimpleXmlObject
     */
    public static function getReferenceTypeListXml()
    {
        $xml = null;
        $bc = new DataAccessComponentReference();
        $list = $bc->getReferenceTypeList();
        return self::_referenceTypeListToXml($list);
    }

}
