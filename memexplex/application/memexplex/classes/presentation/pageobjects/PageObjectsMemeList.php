<?php

/**
 * Gathers all the business objects needed by an HtmlContent Object
 * and puts them all together in a single SimpleXmlObject.
 *
 * @package MemexPlex
 * @subpackage Business
 * @author Professor Ned Brainard
 * @see PageObjectsInterface
 */

class PageObjectsMemeList
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

            $memeListXml = BusinessObjectMeme::getMemeListXml($this->searchParameters);
            $xmlData->appendChild($memeListXml);
            Benchmark::setBenchmark('getMemeXml', __FILE__, __LINE__);
            
            $totalRows = 0;
            if ($memeListXml->TotalRows)
            {
                $totalRows = $memeListXml->TotalRows;
            }
            
            $punpOpts = "";
            if ($this->searchParameters->getUid())
            {
                $punpOpts = "
		<option><value>published</value><description>Published</description></option>
		<option><value>unpublished</value><description>Unpublished</description></option>";
            }
            
            $filters = new SimpleXmlObject
            (
"
<Filters>
	<SearchFilter>$punpOpts
		<option><value>orphaned</value><description>Referenceless</description></option>
		<option><value>schemaless</value><description>Schemaless</description></option>
	</SearchFilter>
	<SortFilter>
		<option><value>date desc,title</value><description>Date Modified</description></option>
		<option><value>date_published desc,date desc,title</value><description>Date Published</description></option>
		<option><value>RAND()</value><description>Random</description></option>
		<option><value>title</value><description>Title</description></option>
	</SortFilter>
	<PagingFilter><TotalRows>$totalRows</TotalRows></PagingFilter>
</Filters>
"
            );
            $xmlData->appendChild($filters);
            Benchmark::setBenchmark('filtersXml', __FILE__, __LINE__);
        }
        catch (GeneralException $e)
        {
            throw new BusinessExceptionPageObject('Data call failed.');
        }

        return $xmlData;
    }
}
