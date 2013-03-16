<?php

/**
 * Gathers all the business objects needed by an HtmlContent Object
 * and puts them all together in a single SimpleXmlObject.
 *
 * @package MemexPlex
 * @subpackage Business
 * @author Dr. Crell Moset
 * @see PageObjectsInterface
 */

class PageObjectsCuratorProfile
implements PageObjectsInterface
{
    /**
     * @var Email of curator we are pulling.
     */
    protected $email=null;

    /**
     * @param string $email
     */
    public function setCuratorEmail($email)
    {
        $this->email = $email;
    }
    
    /**
     * @return SimpleXmlObject
     */
    public function getData()
    {
        try
        {
            $xmlData = new SimpleXmlObject("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<base></base>");
            
            if (isset($this->email))
            {
                // CURATOR
                $curatorXml = BusinessObjectCurator::getCuratorXml($this->email);
                $xmlData->appendChild($curatorXml);
                Benchmark::setBenchmark('getCuratorXml', __FILE__, __LINE__);
            }
            else
            {
                throw new BusinessExceptionPageObject('Curator not logged in.');
            }

        }
        catch (GeneralException $e)
        {
            throw new BusinessExceptionPageObject('Data call failed.');
        }

        return $xmlData;
    }
}
