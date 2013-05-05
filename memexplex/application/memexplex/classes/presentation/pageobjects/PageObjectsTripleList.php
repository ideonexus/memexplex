<?php

/**
 * Gathers all the business objects needed by an HtmlContent Object
 * and puts them all together in a single SimpleXmlObject.
 *
 * @package MemexPlex
 * @subpackage Business
 * @author Dr. Finkelstein
 * @see PageObjectsInterface
 */

class PageObjectsTripleList
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
            $tripleListXml = BusinessObjectTriple::getTripleListXml($this->searchParameters);
            $xmlData->appendChild($tripleListXml);
            Benchmark::setBenchmark('getTripleXml', __FILE__, __LINE__);
            
            $totalRows = 0;
            if ($tripleListXml->TotalRows)
            {
                $totalRows = $tripleListXml->TotalRows;
            }
            
            $punpOpts = "";
            if ($this->searchParameters->getUid())
            {
                $punpOpts = "
		<option><value>all</value><description>All</description></option>
		<option><value>published</value><description>Published</description></option>
		<option><value>unpublished</value><description>Unpublished</description></option>";
            }
            
            $filters = new SimpleXmlObject
            (
"
<Filters>
	<SearchFilter>$punpOpts
		<option><value>1</value><description>Pred: Similarity</description></option>
		<option><value>2</value><description>Pred: Contrast</description></option>
		<option><value>3</value><description>Pred: Comparison</description></option>
		<option><value>4</value><description>Pred: Time Sequence</description></option>
		<option><value>5</value><description>Pred: Example/Illust.</description></option>
		<option><value>6</value><description>Pred: Emphasis</description></option>
		<option><value>7</value><description>Pred: Place/Position</description></option>
		<option><value>8</value><description>Pred: Cause/Effect</description></option>
		<option><value>9</value><description>Pred: Support/Evidence</description></option>
		<option><value>10</value><description>Pred: Concession</description></option>
		<option><value>11</value><description>Pred: Summary</description></option>
		<option><value>12</value><description>Pred: Conclusion</description></option>
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
