<?php

/**
 * Gathers all the business objects needed by an HtmlContent Object
 * and puts them all together in a single SimpleXmlObject.
 *
 * @package MemexPlex
 * @subpackage Business
 * @author Dr. Evil
 * @see PageObjectsInterface
 */

class PageObjectsTriple
implements PageObjectsInterface
{
    /**
     * @var Search Parameters to filter results.
     */
    protected $searchParameters=null;

    /**
     * @param MemexPlexObjectSearchParameters $searchParameters
     */
    public function setSearchParameters(MemexPlexObjectSearchParameters $searchParameters)
    {
        $this->searchParameters = $searchParameters;
    }
    
    /**
     * @return SimpleXmlObject
     */
    public function getData()
    {
        try
        {
            $xmlData = new SimpleXmlObject("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<base></base>");
            
            if (isset($this->searchParameters)
                && $this->searchParameters->getId())
            {
                $tripleXml = BusinessObjectTriple::getTripleListXml($this->searchParameters);
                $xmlData->appendChild($tripleXml);
                Benchmark::setBenchmark('getTripleListXml', __FILE__, __LINE__);
            }
            
            //PREDICATES
            $predicateListXml = BusinessObjectTriple::getPredicateListXml();
            $xmlData->appendChild($predicateListXml);
            Benchmark::setBenchmark('getPredicateListXml', __FILE__, __LINE__);
        }
        catch (GeneralException $e)
        {
            throw new BusinessExceptionPageObject('Data call failed.');
        }

        return $xmlData;
    }

}
