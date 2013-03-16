<?php

/**
 * Converts Triple and triple-related objects to SimpleXmlObjects.
 *
 * @package MemexPlex
 * @subpackage Business
 * @author TMNT Donatello
 */

class BusinessObjectTriple
{

    /**
     * Converts a Triple object to a SimpleXmlObject.
     *
     * @param Triple $triple
     * @return SimpleXmlObject
     */
    public static function _tripleToXml(
        Triple $triple
    )
    {
        $xml = new SimpleXmlObject('<Triple/>');

        $xml->Id            = $triple->getId();
        $xml->Title         = $triple->getTitle();
        $xml->Description   = $triple->getDescription();
        $xml->Published     = $triple->getPublished();
        $xml->DatePublished = $triple->getDatePublished();
        
        if ($triple->getCurator())
        {
            $xml->appendChild(BusinessObjectCurator::_curatorToXml($triple->getCurator()));
        }

        $subjectxml = $xml->addChild('Subject');
        $subjectxml->appendChild(BusinessObjectMeme::_memeToXml($triple->getSubjectMeme()));

        $predicatexml              = $xml->addChild('Predicate');
        $predicate                 = $triple->getPredicate();
        $predicatexml->Id          = $predicate->getId();
        $predicatexml->Description = $predicate->getDescription();

        $objectxml = $xml->addChild('Object');
        $objectxml->appendChild(BusinessObjectMeme::_memeToXml($triple->getObjectMeme()));

        $taxolist    = $triple->getTaxonomyList();
        $taxolistxml = $xml->addChild('TaxonomyList');
        foreach ($taxolist as $item)
        {
            $taxolistxml->addChild('Taxonomy',$item->getText());
        }

        return $xml;
    }

    /**
     * Converts a TripleList object to a SimpleXmlObject.
     *
     * @param TripleList $list
     * @return SimpleXmlObject
     */
    public static function _tripleListToXml($list=null)
    {
        if ($list instanceof TripleList)
        {
            $xml = new SimpleXmlObject('<TripleList/>');

            foreach ($list as $item)
            {
                $xml->appendChild(self::_tripleToXml($item));
            }

            $xml->TotalRows = $list->getTotalRows();
        }
        else
        {
            $xml = new SimpleXmlObject("<TripleList><Triple></Triple></TripleList>");
        }

        return $xml;
    }

    /**
     * Converts a Predicate object to a SimpleXmlObject.
     *
     * @param Predicate $predicate
     * @return SimpleXmlObject
     */
    public static function _predicateToXml(
        Predicate $predicate
    )
    {
        $xml = new SimpleXmlObject('<Predicate/>');

    	$xml->Id          = $predicate->getId();
    	$xml->Description = $predicate->getDescription();

        return $xml;
    }

    /**
     * Converts a PredicateList object to a SimpleXmlObject.
     *
     * @param PredicateList $list
     * @return SimpleXmlObject
     */
    public static function _predicateListToXml($list)
    {
        if ($list instanceof PredicateList)
        {
            $xml = new SimpleXmlObject('<PredicateList/>');

            foreach ($list as $item)
            {
                $xml->appendChild(self::_predicateToXml($item));
            }
        }
        else
        {
            $xml = new SimpleXmlObject("<PredicateList><Predicate></Predicate></PredicateList>");
        }
        return $xml;
    }

    /**
     * Returns a TripleList based on submitted search parameters.
     *
     * @param MemexPlexObjectSearchParameters $searchParameters
     * @return SimpleXmlObject
     */
    public static function getTripleListXml(MemexPlexObjectSearchParameters $searchParameters)
    {
        $xml = null;
        $bc = new DataAccessComponentTriple;
        $list = $bc->getTripleList($searchParameters);
        return self::_tripleListToXml($list);
    }

    /**
     * Returns a List of Triples associated to a Meme.
     *
     * @param integer $memeid Meme database id.
     * @return SimpleXmlObject
     */
    public static function getTripleListByMemeIdXml($memeid,$publishedOnly=false)
    {
        $xml = null;
        $bc = new DataAccessComponentTriple();
        $list = $bc->getTripleListByMemeId($memeid,$publishedOnly);
        return self::_tripleListToXml($list);
    }

    /**
     * Returns a List of Predicate options.
     *
     * @return SimpleXmlObject
     */
    public static function getPredicateListXml()
    {
        $xml = null;
        $bc = new DataAccessComponentTriple();
        $list = $bc->getPredicateList();
        return self::_predicateListToXml($list);
    }

}
