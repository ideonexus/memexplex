<?php
/**
 * A triple relates two memes semantically via a subject-predicate-object
 * relationship, similar to an RDF triple, but without all the semantic-web
 * bureaucracy.
 *
 * @package MemexPlex
 * @subpackage Business.Entity
 * @see MemexPlexObject
 * @author Kilgore Trout
 */
class Triple extends MemexPlexObject
{

    /**
     * @var string <todo:description>
     */
    protected $description;

    /**
     * @var Meme <todo:description>
     */
    protected $subjectMeme;

    /**
     * @var Predicate <todo:description>
     */
    protected $predicate;

    /**
     * @var Meme <todo:description>
     */
    protected $objectMeme;

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
     * Triple-Specific Properties.
     * @param string $description
     * @param Meme $subjectMeme
     * @param Predicate $predicate
     * @param Meme $objectMeme
     */
    public function __construct(
        $id             = null
        ,$title         = null
        ,$curator       = null
        ,$published     = null
        ,$datePublished = null
        ,$date          = null
        ,$taxonomyList  = null
        
        ,$description   = null
        ,$subjectMeme   = null
        ,$predicate     = null
        ,$objectMeme    = null
    )
    {
        $this->setId($id);
        $this->setTitle($title);
        $this->setCurator($curator);
        $this->setPublished($published);
        $this->setDatePublished($datePublished);
        $this->setDate($date);
        $this->setTaxonomyList($taxonomyList);
        
        $this->setDescription($description);
        $this->setSubjectMeme($subjectMeme);
        $this->setPredicate($predicate);
        $this->setObjectMeme($objectMeme);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string
     */
    public function setDescription($description="")
    {
        $this->description = $description;
    }

    /**
     * @return Meme
     */
    public function getSubjectMeme()
    {
        return $this->subjectMeme;
    }
    
    /**
     * @param Meme
     */
    public function setSubjectMeme($subjectMeme=null)
    {
        if ($subjectMeme)
        {
            if (!$subjectMeme instanceof Meme)
            {
                throw new BusinessExceptionInvalidArgument
                (
                	'Attempt to add non Meme object to Triple SubjectMeme.'
                );
                return false;
            }
            else
            {
                $this->subjectMeme = $subjectMeme;
            }
        }
    }

    /**
     * @return Predicate
     */
    public function getPredicate()
    {
        return $this->predicate;
    }
    
    /**
     * @param Predicate
     */
    public function setPredicate($predicate=null)
    {
        if ($predicate)
        {
            if (!$predicate instanceof Predicate)
            {
                throw new BusinessExceptionInvalidArgument
                (
                	'Attempt to add non Predicate object to Triple Predicate.'
                );
                return false;
            }
            else
            {
                $this->predicate = $predicate;
            }
        }
    }

    /**
     * @return Meme
     */
    public function getObjectMeme()
    {
        return $this->objectMeme;
    }
    
    /**
     * @param Meme
     */
    public function setObjectMeme($objectMeme=null)
    {
        if ($objectMeme)
        {
            if (!$objectMeme instanceof Meme)
            {
                throw new BusinessExceptionInvalidArgument
                (
                	'Attempt to add non Meme object to Triple ObjectMeme.'
                );
                return false;
            }
            else
            {
                $this->objectMeme = $objectMeme;
            }
        }
    }

    /**
     * This function compares the titles of two
     * Triples and returns 1 or 0 depending on
     * if they are in alphabetical order or not.
     * It is used by the MemeList to sort.
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
        $compare = strnatcasecmp
        (
            $a->title
            ,$b->title
        );

        return $compare;
    }

}
