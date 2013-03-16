<?php

/**
 * Gathers all the business objects needed by an HtmlContent Object
 * and puts them all together in a single SimpleXmlObject.
 *
 * @package MemexPlex
 * @subpackage Business
 * @author Vergil Ulam
 * @see PageObjectsInterface
 */

class PageObjectsAmazonSearch
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
                $obj = new AmazonProductAPI();

                $pageFilter = 1;
                if ($this->searchParameters->getPageFilter())
                {
                    $pageFilter = $this->searchParameters->getPageFilter();
                }
 
                try
                {
                    $amzresult = $obj->searchProducts(
                        $this->searchParameters->getSearchString()
                        ,$this->searchParameters->getCategoryFilter()
                        ,"Keywords"
                        ,$pageFilter
                    );
                }
                catch(Exception $e)
                {
                    echo $e->getMessage();
                }
                $xmlData->appendChild($amzresult);
                Benchmark::setBenchmark('Amazon Results.', __FILE__, __LINE__);
                
                $totalRows = $amzresult->Items->TotalResults;
            }

            $filters = new SimpleXmlObject
            (
"
<Filters>
	<!--TypeFilter>
		<option><class>All</class><value>Keywords</value><description>Keywords</description></option>
		<option><class>All</class><value>Title</value><description>Title</description></option>
		<option><class>Books</class><value>Author</value><description>Author</description></option>
	</TypeFilter-->
	<CategoryFilter>
		<option><value>Books</value><description>Books</description></option>
		<option><value>DVD</value><description>DVD</description></option>
		<option><value>Software</value><description>Software</description></option>
		<option><value>Music</value><description>Music</description></option>
	</CategoryFilter>
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
