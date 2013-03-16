<?php

/**
 * Gathers all the business objects needed by an HtmlContent Object
 * and puts them all together in a single SimpleXmlObject.
 *
 * @package MemexPlex
 * @subpackage Business
 * @author Dr. Lazlo Zand
 * @see PageObjectsInterface
 */

class PageObjectsMeme
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
                // MEME
                $memeXml = BusinessObjectMeme::getMemeListXml($this->searchParameters);
                $xmlData->appendChild($memeXml);
                Benchmark::setBenchmark('getMemeListXml', __FILE__, __LINE__);
                
                $ownerid = $pageObjectsXml->MemeList->Meme->Curator->Id;
                $publishedOnly = true;
                if (CuratorSession::checkEditPrivileges($ownerid))
                {
                    $publishedOnly = false;
                }
                
                // REFERENCE
                $referenceXml = BusinessObjectReference::getReferenceByMemeIdXml($this->searchParameters->getId());
                $xmlData->appendChild($referenceXml);
                Benchmark::setBenchmark('getReferenceByMemeIdXml', __FILE__, __LINE__);
                
                // TRIPLES
                $tripleXml = BusinessObjectTriple::getTripleListByMemeIdXml(
                    $this->searchParameters->getId()
                    ,$publishedOnly
                );
                $xmlData->appendChild($tripleXml);
                Benchmark::setBenchmark('getTripleListByMemeIdXml', __FILE__, __LINE__);
                
                // SCHEMAS
                $schemaXml = BusinessObjectSchema::getSchemaListByMemeIdXml(
                    $this->searchParameters->getId()
                    ,$publishedOnly
                );
                $xmlData->appendChild($schemaXml);
                Benchmark::setBenchmark('getSchemaListByMemeIdXml', __FILE__, __LINE__);
            }
            else
            {
                // DUMMY MEME
                $memeXml = new SimpleXmlObject('<MemeList><Meme><Id>0</Id></Meme></MemeList>');
                $xmlData->appendChild($memeXml);
            }
        }
        catch (GeneralException $e)
        {
            throw new BusinessExceptionPageObject('Data call failed.');
        }

        return $xmlData;
    }
}
