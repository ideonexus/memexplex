<?php

/**
 * Gathers all the business objects needed by an HtmlContent Object
 * and puts them all together in a single SimpleXmlObject.
 *
 * @package MemexPlex
 * @subpackage Business
 * @author Dr. Emilio Lizardo
 * @see PageObjectsInterface
 */

class PageObjectsReference
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
                //REFERENCE
                $referenceXml = BusinessObjectReference::getReferenceListXml($this->searchParameters);
                $xmlData->appendChild($referenceXml);
                Benchmark::setBenchmark('getReferenceListXml', __FILE__, __LINE__);

                // MEME LIST
                $memeListXml = BusinessObjectMeme::getMemeListByReferenceIdXml($this->searchParameters->getId());
                $xmlData->appendChild($memeListXml);
                Benchmark::setBenchmark('getMemeListByReferenceIdXml', __FILE__, __LINE__);
                
                // PARENT REFERENCE LIST
                $parentReferenceXml = BusinessObjectReference::getReferenceListByChildReferenceIdXml($this->searchParameters->getId());
                $baseParentReferenceXml = new SimpleXmlObject('<ParentReferenceList/>');
                $baseParentReferenceXml->appendChild($parentReferenceXml);
                $xmlData->appendChild($baseParentReferenceXml);
                Benchmark::setBenchmark('getReferenceListByChildReferenceIdXml', __FILE__, __LINE__);

                // CHILD REFERENCE LIST
                $childReferenceXml = BusinessObjectReference::getReferenceListByParentReferenceIdXml($this->searchParameters->getId());
                if ($childReferenceXml)
                {
                    foreach ($childReferenceXml->Reference as $reference)
                    {
                        // MEME LIST
                        $memeXml = BusinessObjectMeme::getMemeListByReferenceIdXml($reference->Id);
                        $reference->appendChild($memeXml);
                        Benchmark::setBenchmark('getMemeListByReferenceIdXml', __FILE__, __LINE__);
                    }
                    $baseChildReferenceXml = new SimpleXmlObject('<ChildReferenceList/>');
                    $baseChildReferenceXml->appendChild($childReferenceXml);
                    $xmlData->appendChild($baseChildReferenceXml);
                    Benchmark::setBenchmark('getReferenceListByParentReferenceIdXml', __FILE__, __LINE__);
                }
            }

            // REFERENCE SUPER TYPES
            $referenceSuperTypeListXml = BusinessObjectReference::getReferenceSuperTypeListXml();
            $xmlData->appendChild($referenceSuperTypeListXml);
            Benchmark::setBenchmark('getReferenceSuperTypeListXml', __FILE__, __LINE__);
            
            // REFERENCE TYPES
            $referenceTypeListXml = BusinessObjectReference::getReferenceTypeListXml();
            $xmlData->appendChild($referenceTypeListXml);
            Benchmark::setBenchmark('getReferenceTypeListXml', __FILE__, __LINE__);
        }
        catch (GeneralException $e)
        {
            throw new BusinessExceptionPageObject('Data call failed.');
        }

        return $xmlData;
    }
}
