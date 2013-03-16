<?php
/**
 * A reference is an academic-style reference. Properties for a reference
 * are consolidated for space, such as Publisher and Periodical sharing the
 * same property space. As these are mostly free-text entries with little 
 * validation, users may choose to make their values as stringent as they
 * prefer.
 *
 * @package MemexPlex
 * @subpackage Business.Entity
 * @see MemexPlexObject
 * @author Seven of Nine
 * [TODO: Reference should have a MemeList property.]
 */
class Reference extends MemexPlexObject
{

	/**
     * @var ReferenceSuperType
     */
    protected $referenceSuperType;

    /**
     * @var ReferenceType
     */
    protected $referenceType;

    /**
     * @var string Authors of the reference.
     */
    protected $authors;

    /**
     * @var string Date of the reference in any string format.
     * [TODO: Figure out how to get this out/in as an actual date for querying.]
     */
    protected $referenceDate;

    /**
     * @var string The location of the refernce's publisher.
     */
    protected $publicationLocation;

    /**
     * @var string The publisher and/or periodical.
     */
    protected $publisherPeriodical;

    /**
     * @var string The Volume and Pages from the periodical or book.
     */
    protected $volumePages;

    /**
     * @var string Direct WWW link to the reference.
     */
    protected $url;

    /**
     * @var string Service or Database providing the reference.
     */
    protected $referenceService;

    /**
     * @var date Date reference was retrieved.
     */
    protected $dateRetrieved;

    /**
     * @var string International Standard Book Number
     */
    protected $isbn;

    /**
     * @var string European Article Number
     */
    protected $ean;

    /**
     * @var string Universal Product Code
     */
    protected $upc;

    /**
     * @var string Link to Amazon image, small.
     */
    protected $smallImageUrl;

    /**
     * @var string Link to Amazon image, large.
     */
    protected $largeImageUrl;

    /**
     * @var string Amazon Standard Identification Number.
     */
    protected $asin;

    /**
     * @var string Amazon product URL.
     */
    protected $amazonUrl;

    /**
     * Called when a new object is instantiated, 
     * accepts all properties as arguments.
     *
     * MemexPlexObject Properties
     * @param integer $id
     * @param string $title
     * @param Curator $curator
     * @param boolean   $published
     * @param date $datePublished
     * @param date $date
     * @param TaxonomyList $taxonomyList
     * 
     * Reference-Specific Properties
     * @param ReferenceSuperType $referenceSuperType
     * @param ReferenceType $referenceType
     * @param string $authors
     * @param string $referenceDate
     * @param string $publicationLocation
     * @param string $publisherPeriodical
     * @param string $volumePages
     * @param string $url
     * @param string $referenceService
     * @param date $dateRetrieved
     * @param string $isbn
     * @param string $ean
     * @param string $upc
     * @param string $smallImageUrl
     * @param string $largeImageUrl
     * @param string $asin
     * @param string $amazonUrl
     * @param int $memeCount
     */
    public function __construct
    (
        $id                    = null
        ,$title                = null
        ,$curator              = null
        ,$published            = null
        ,$datePublished        = null
        ,$date                 = null
        ,$taxonomyList         = null
        
        ,$referenceSuperType   = null
        ,$referenceType        = null
        ,$authors              = null
        ,$referenceDate        = null
        ,$publicationLocation  = null
        ,$publisherPeriodical  = null
        ,$volumePages          = null
        ,$url                  = null
        ,$referenceService     = null
        ,$dateRetrieved        = null
        ,$isbn                 = null
        ,$ean                  = null
        ,$upc                  = null
        ,$smallImageUrl        = null
        ,$largeImageUrl        = null
        ,$asin                 = null
        ,$amazonUrl            = null
        ,$memeCount            = null
    )
    {
        $this->setId($id);
        $this->setTitle($title);
        $this->setCurator($curator);
        $this->setPublished($published);
        $this->setDatePublished($datePublished);
        $this->setDate($date);
        $this->setTaxonomyList($taxonomyList);
        
        $this->setReferenceSuperType($referenceSuperType);
        $this->setReferenceType($referenceType);
        $this->setAuthors($authors);
        $this->setReferenceDate($referenceDate);
        $this->setPublicationLocation($publicationLocation);
        $this->setPublisherPeriodical($publisherPeriodical);
        $this->setVolumePages($volumePages);
        $this->setUrl($url);
        $this->setReferenceService($referenceService);
        $this->setDateRetrieved($dateRetrieved);
        $this->setIsbn($isbn);
        $this->setEan($ean);
        $this->setUpc($upc);
        $this->setSmallImageUrl($smallImageUrl);
        $this->setLargeImageUrl($largeImageUrl);
        $this->setAsin($asin);
        $this->setAmazonUrl($amazonUrl);
        $this->setMemeCount($memeCount);
    }

    /**
     * @return ReferenceSuperType
     */
    public function getReferenceSuperType()
    {
        return $this->referenceSuperType;
    }

    /**
     * @param ReferenceSuperType
     */
    public function setReferenceSuperType($referenceSuperType=null)
    {
        if (!$referenceSuperType instanceof ReferenceSuperType)
        {
            throw new BusinessExceptionInvalidArgument
            (
            	'Attempt to add non ReferenceSuperType object to Reference.'
            );
            return false;
        }
        else
        {
            $this->referenceSuperType = $referenceSuperType;
        }
    }

    /**
     * @return ReferenceType
     */
    public function getReferenceType()
    {
        return $this->referenceType;
    }

    /**
     * @param ReferenceType
     */
    public function setReferenceType($referenceType=null)
    {
        if (!$referenceType instanceof ReferenceType)
        {
            throw new BusinessExceptionInvalidArgument
            (
            	'Attempt to add non Reference Type object to Reference.'
            );
            return false;
        }
        else
        {
            $this->referenceType        = $referenceType;
        }
    }

    /**
     * @param AuthorList
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @param AuthorList
     * @return string
     */
    public function setAuthors($authors="")
    {
        if (!$authors instanceof AuthorList)
        {
            throw new BusinessExceptionInvalidArgument
            (
            	'Attempt to add non AuthorList object to Reference.'
            );
            return false;
        }
        else
        {
            $this->authors = $authors;
        }
    }

    /**
     * @return string
     */
    public function getReferenceDate()
    {
        return $this->referenceDate;
    }

    /**
     * @param string
     */
    public function setReferenceDate($referenceDate="")
    {
        $this->referenceDate = $referenceDate;
    }

    /**
     * @return string
     */
    public function getPublicationLocation()
    {
        return $this->publicationLocation;
    }

    /**
     * @param string
     */
    public function setPublicationLocation($publicationLocation="")
    {
        $this->publicationLocation = $publicationLocation;
    }

    /**
     * @return string
     */
    public function getPublisherPeriodical()
    {
        return $this->publisherPeriodical;
    }

    /**
     * @param string
     */
    public function setPublisherPeriodical($publisherPeriodical="")
    {
        $this->publisherPeriodical = $publisherPeriodical;
    }

    /**
     * @return string
     */
    public function getVolumePages()
    {
        return $this->volumePages;
    }

    /**
     * @param string
     */
    public function setVolumePages($volumePages="")
    {
        $this->volumePages = $volumePages;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string
     */
    public function setUrl($url="")
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getReferenceService()
    {
        return $this->referenceService;
    }

    /**
     * @param string
     */
    public function setReferenceService($referenceService="")
    {
        $this->referenceService = $referenceService;
    }

    /**
     * @return string
     */
    public function getDateRetrieved()
    {
        return date("Y-m-d H:i:s",$this->dateRetrieved);
    }

    /**
     * @param date
     */
    public function setDateRetrieved($date=null)
    {
        if ($date)
        {
            $this->dateRetrieved = strtotime($date);
        }
        else
        {
            $this->dateRetrieved = time();
        }
    }
    
    /**
     * @return string
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * @param string
     */
    public function setIsbn($isbn="")
    {
        $this->isbn = $isbn;
    }

    /**
     * @return string
     */
    public function getEan()
    {
        return $this->ean;
    }

    /**
     * @param string
     */
    public function setEan($ean="")
    {
        $this->ean = $ean;
    }

    /**
     * @return string
     */
    public function getUpc()
    {
        return $this->upc;
    }

    /**
     * @param string
     */
    public function setUpc($upc="")
    {
        $this->upc = $upc;
    }

    /**
     * @return string
     */
    public function getSmallImageUrl()
    {
        return $this->smallImageUrl;
    }

    /**
     * @param string
     */
    public function setSmallImageUrl($smallImageUrl="")
    {
        $this->smallImageUrl = $smallImageUrl;
    }

    /**
     * @return string
     */
    public function getLargeImageUrl()
    {
        return $this->largeImageUrl;
    }
    
    /**
     * @param string
     */
    public function setLargeImageUrl($largeImageUrl="")
    {
        $this->largeImageUrl = $largeImageUrl;
    }
    
    /**
     * @return string
     */
    public function getAsin()
    {
        return $this->asin;
    }
    
    /**
     * @param string
     */
    public function setAsin($asin="")
    {
        $this->asin = $asin;
    }
    
    /**
     * @return string
     */
    public function getAmazonUrl()
    {
        return $this->amazonUrl;
    }
    
    /**
     * @param string
     */
    public function setAmazonUrl($amazonUrl="")
    {
        $this->amazonUrl = $amazonUrl;
    }
    
    /**
     * @return int
     */
    public function getMemeCount()
    {
        return $this->memeCount;
    }

    /**
     * @param int
     */
    public function setMemeCount($memeCount=0)
    {
        $this->memeCount = $memeCount;
    }

    /**
     * This function compares the titles of two
     * References and returns 1 or 0 depending on
     * if they are in alphabetical order or not.
     * It is used by the MemeList to sort by name.
     *
     * @param this $a
     * @param this $b
     * @return bool
     */
    public static function compare
    (
        self $a
        ,self $b
    )
    {
		//Compare names using a case-insensitive
		//"natural order" algorithm
        $compare = strnatcasecmp
        (
            $a->title
            ,$b->title
        );

        return $compare;
    }

}
