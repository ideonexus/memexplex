<?php

/**
 * Gathers all the business objects needed by an HtmlContent Object
 * and puts them all together in a single SimpleXmlObject.
 *
 * @package MemexPlex
 * @subpackage Business
 * @author Edward Nigma
 * @see PageObjectsInterface
 */

class PageObjectsSchema
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
                //SCHEMA
                $schemaXml = BusinessObjectSchema::getSchemaListXml($this->searchParameters);
                $xmlData->appendChild($schemaXml);
                Benchmark::setBenchmark('getSchemaListXml', __FILE__, __LINE__);
                
                // MEME LIST
                $memeXml = BusinessObjectMeme::getMemeListBySchemaIdXml($this->searchParameters->getId());
                $xmlData->appendChild($memeXml);
                Benchmark::setBenchmark('getMemeListBySchemaIdXml', __FILE__, __LINE__);

                // REFERENCE LIST
                $memeXml = BusinessObjectReference::getReferenceListBySchemaIdXml($this->searchParameters->getId());
                $xmlData->appendChild($memeXml);
                Benchmark::setBenchmark('getReferenceListBySchemaIdXml', __FILE__, __LINE__);
                
                // PARENT SCHEMA LIST
                $parentSchemaXml = BusinessObjectSchema::getSchemaListByChildSchemaIdXml($this->searchParameters->getId());
                $baseParentSchemaXml = new SimpleXmlObject('<ParentSchemaList/>');
                $baseParentSchemaXml->appendChild($parentSchemaXml);
                $xmlData->appendChild($baseParentSchemaXml);
                Benchmark::setBenchmark('getSchemaListByChildSchemaIdXml', __FILE__, __LINE__);

                // CHILD SCHEMA LIST
                $childSchemaXml = BusinessObjectSchema::getSchemaListByParentSchemaIdXml($this->searchParameters->getId());
                $baseChildSchemaXml = new SimpleXmlObject('<ChildSchemaList/>');
                $baseChildSchemaXml->appendChild($childSchemaXml);
                $xmlData->appendChild($baseChildSchemaXml);
                Benchmark::setBenchmark('getSchemaListByParentSchemaIdXml', __FILE__, __LINE__);
            }

        }
        catch (GeneralException $e)
        {
            throw new BusinessExceptionPageObject('Data call failed.');
        }

        return $xmlData;
    }

}
