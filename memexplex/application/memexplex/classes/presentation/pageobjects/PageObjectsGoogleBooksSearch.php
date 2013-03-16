<?php

/**
 * Gathers all the business objects needed by an HtmlContent Object
 * and puts them all together in a single SimpleXmlObject.
 *
 * @package MemexPlex
 * @subpackage Business
 * @author Locutus
 * @see PageObjectsInterface
 */

class PageObjectsGoogleBooksSearch
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
            $totalRows = "";
            if ($this->searchParameters->getSearchString())
            {
                $obj = new GoogleBooksAPI();

                $pageFilter = 1;
                if ($this->searchParameters->getPageFilter())
                {
                    $pageFilter = $this->searchParameters->getPageFilter();
                }
 
                try
                {
                    $googleResult = $obj->searchBooks(
                        $this->searchParameters->getSearchString()
                        ,$pageFilter
                    );
                }
                catch(Exception $e)
                {
                    echo $e->getMessage();
                }
                $xmlData->appendChild($googleResult);
                Benchmark::setBenchmark('Google Books Results.', __FILE__, __LINE__);

                //Disables Paging until I can get back to it, just show 100 resuls max
                //$totalRows = Count($googleResult->entry);
                $totalRows = 0;
            }

            $filters = new SimpleXmlObject
            (
"
<Filters>
	<PagingFilter><TotalRows>{$totalRows}</TotalRows></PagingFilter>
</Filters>
"
            );
            $xmlData->appendChild($filters);
            Benchmark::setBenchmark('filtersXml', __FILE__, __LINE__);
            
//            $getVariables = BusinessObjectGetVariables::getVariablesToXml();
//            SimpleXml::simpleXmlAppend($xmlData, $getVariables);
        }
        catch (GeneralException $e)
        {
            throw new BusinessExceptionPageObject('Data call failed.');
        }

        return $xmlData;
    }
}
