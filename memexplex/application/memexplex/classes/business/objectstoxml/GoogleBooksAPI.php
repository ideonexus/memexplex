<?php
/**
 * Class to access Google Books API
 * @author Zardoz
 * @link http://code.google.com/intl/nb/apis/books/docs/gdata/developers_guide_php.html#SearchingForBooks
 * 
 */

class GoogleBooksAPI
{

    /**
     * Google Books API URL
     *
     * @author ideonexus
     * @access private
     * @var string
     */
    private $queryUrl    = "http://www.google.com/books/feeds/volumes?";

    protected function  queryGoogleBooks($params)
    {
    	$host = "www.google.com";
    	$uri = "/books/feeds/volumes";
    	
        $canonicalized_query = array();

        foreach ($params as $param=>$value)
        {
            $param = str_replace(" ", "+", rawurlencode($param));
            $value = str_replace(" ", "+", rawurlencode($value));
            $canonicalized_query[] = $param."=".$value;
        }

        $canonicalized_query = implode("&", $canonicalized_query);
    	
    	/* create request */
        $request = "http://".$host.$uri."?".$canonicalized_query;

        $xml_response = file_get_contents($request);

        if ($xml_response === False)
        {
            return False;
        }
        else
        {
            /* parse XML - Replace Namespaces because they screw up simplexmlelements for xpaths */
            $parsed_xml = SimpleXmlObject::loadString(str_replace('xmlns=', 'ns=', $xml_response));
            return ($parsed_xml === False) ? False : $parsed_xml;
        }
    }

    /**
     * Check if the xml received is valid
     *
     * @param mixed $response xml response to check
     * @return bool false if the xml is invalid
     * @return mixed the xml response if it is valid
     * @return exception if we could not connect
     */
    protected function verifyXmlResponse($response)
    {
        if ($response === False)
        {
            throw new Exception("Could not connect to Google Books");
        }
        else
        {
            if (isset($response->entry->title))
            {
                return ($response);
            }
            else
            {
                throw new Exception("Invalid xml response from Google Books.");
            }
        }
    }

    /**
     * Return details of books searched
     *
     * @param string $search search term
     * @param string $page page of search results
     * @return mixed simpleXML object
     */
    public function searchBooks($search, $page = 1)
    {
/**
 * Search Parameters are:
 * q - keywords (eg. "spy plane" ='s %22spy+plane%22
 * start-index - start index (duh)
 * max-results - max results returned (eg. start-index=11&max-results=10)
 */
    	$parameters = array("q"            => $search
                            //,"start-index" => $page
                            ,"max-results" => "100"
                            );

        $xml_response = $this->queryGoogleBooks($parameters);

        return $this->verifyXmlResponse($xml_response);

    }


    /**
     * Return details of a product searched by UPC
     *
     * @param int $upc_code UPC code of the product to search
     * @param string $product_type type of the product
     * @return mixed simpleXML object
     */
    public function getItemByUpc($upc_code, $product_type)
    {
        $parameters = array("Operation"     => "ItemLookup",
                            "ItemId"        => $upc_code,
                            "SearchIndex"   => $product_type,
                            "IdType"        => "UPC",
                            "ResponseGroup" => "Medium");

        $xml_response = $this->queryAmazon($parameters);

        return $this->verifyXmlResponse($xml_response);

    }


    /**
     * Return details of a product searched by ASIN
     *
     * @param int $asin_code ASIN code of the product to search
     * @return mixed simpleXML object
     */
    public function getItemByAsin($asin_code)
    {
        $parameters = array("Operation"     => "ItemLookup",
                            "ItemId"        => $asin_code,
                            "ResponseGroup" => "Medium");

        $xml_response = $this->queryAmazon($parameters);

        return $this->verifyXmlResponse($xml_response);
    }


    /**
     * Return details of a product searched by keyword
     *
     * @param string $keyword keyword to search
     * @param string $product_type type of the product
     * @return mixed simpleXML object
     */
    public function getItemByKeyword($keyword, $product_type)
    {
        $parameters = array("Operation"   => "ItemSearch",
                            "Keywords"    => $keyword,
                            "SearchIndex" => $product_type);

        $xml_response = $this->queryAmazon($parameters);

        return $this->verifyXmlResponse($xml_response);
    }

}
