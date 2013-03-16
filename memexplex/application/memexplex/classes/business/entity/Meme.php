<?php

/**
 * The meme is a basic unit of cultural imitation, a discrete idea. Originally
 * defined by Richard Dawkins in his book "The Selfish Gene," the concept was
 * more thoroughly explored in Susan Blackmore's "The Meme Machine," where the
 * evolutionary origins of human imitation were hypothesized and the many
 * potential insights memetic theory could provide on modern culture were
 * explored.
 *
 * @package MemexPlex
 * @subpackage Business.Entity
 * @see MemexPlexObject
 * @author Data
 */
class Meme extends MemexPlexObject
{

    /**
     * @var string Commentary or summarization of the meme.
     */
    protected $text;

    /**
     * @var string A direct blockquote from the reference, the raw meme.
     */
    protected $quote;

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
     * Meme-Specific Properties.
     * @param string $text
     * @param string $quote
     * @param int $referenceCount
     * @param int $tripleCount
     * @param int $schemaCount
     */
    public function __construct(
        $id             = null
    	,$title         = null
        ,$curator       = null
        ,$published     = null
        ,$datePublished = null
        ,$date          = null
        ,$taxonomyList  = null
        
        ,$text          = null
        ,$quote         = null
        ,$referenceCount= null
        ,$tripleCount   = null
        ,$schemaCount   = null
    )
    {
        $this->setId($id);
        $this->setTitle($title);
        $this->setCurator($curator);
        $this->setPublished($published);
        $this->setDatePublished($datePublished);
        $this->setDate($date);
        $this->setTaxonomyList($taxonomyList);
        
        $this->setText($text);
        $this->setQuote($quote);
        $this->setReferenceCount($referenceCount);
        $this->setTripleCount($tripleCount);
        $this->setSchemaCount($schemaCount);
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string 10,000 Characters max.
     * [TODO: Validate character length.]
     */
    public function setText($text="")
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * @param string 10,000 Characters max.
     * [TODO: Validate character length.]
     */
    public function setQuote($quote="")
    {
        $this->quote = $quote;
    }

    /**
     * @return int
     */
    public function getReferenceCount()
    {
        return $this->referenceCount;
    }

    /**
     * @param int
     */
    public function setReferenceCount($referenceCount=0)
    {
        $this->referenceCount = $referenceCount;
    }

    /**
     * @return int
     */
    public function getTripleCount()
    {
        return $this->tripleCount;
    }

    /**
     * @param int
     */
    public function setTripleCount($tripleCount=0)
    {
        $this->tripleCount = $tripleCount;
    }

    /**
     * @return int
     */
    public function getSchemaCount()
    {
        return $this->schemaCount;
    }

    /**
     * @param int
     */
    public function setSchemaCount($schemaCount=0)
    {
        $this->schemaCount = $schemaCount;
    }

    /**
     * This function compares the titles of two
     * Memes and returns 1 or 0 depending on
     * if they are in alphabetical order or not.
     * It is used by the MemeList to sort.
     *
     * @param this $a
     * @param this $b
     * @return bool
     */
    public static function compare(
        self $a
        ,self $b
    )
    {
        $compare = strnatcasecmp
        (
            $a->title
            ,$b->title
        );

        return $compare;
    }

}
