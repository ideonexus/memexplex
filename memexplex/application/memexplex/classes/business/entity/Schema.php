<?php
/**
 * A schema encapsulates a set of semantically-related memes and sub-schemas.
 * It's a way of collecting a set of ideas and sets of ideas in a single place
 * for constructing a more elaborate macro meme.
 *
 * @package MemexPlex
 * @subpackage Business.Entity
 * @see MemexPlexObject
 * @author Capt. John Joseph Yossarian
 * [TODO: Meme Sequencing, order memes in a way useful to constructing an argument.]
 */
class Schema extends MemexPlexObject
{

    /**
     * @var string Description of the schema, what relates all its memes.
     */
    protected $description;

    /**
     * @var MemeList A list of memes contained within the schema.
     */
    protected $memeList;

    /**
     * @var SchemaList A list of child schemas to this one.
     * [TODO: Actually use this property. Currently application circumvents it.]
     */
    protected $schemaList;

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
     * Schema-Specific Properties.
     * @param string $description
     * @param MemeList $memeList
     * @param SchemaList $schemaList
     * @param int $memeCount
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
        ,$memeList      = null
        ,$schemaList    = null
        ,$memeCount     = null
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
        $this->setMemeList($memeList);
        $this->setSchemaList($schemaList);
        $this->setMemeCount($memeCount);
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
     * @return MemeList
     */
    public function getMemeList()
    {
        return $this->memeList;
    }
    
    /**
     * @param MemeList
     */
    public function setMemeList($memeList=null)
    {
        if ($memeList)
        {
            if (!$memeList instanceof MemeList)
            {
                throw new BusinessExceptionInvalidArgument
                (
                	'Attempt to add non MemeList object to Schema.'
                );
                return false;
            }
            else
            {
                $this->memeList = $memeList;
            }
        }
    }

    /**
     * @return SchemaList
     */
    public function getSchemaList()
    {
        return $this->schemaList;
    }

    /**
     * @param SchemaList
     */
    public function setSchemaList($schemaList=null)
    {
        if ($schemaList)
        {
            if (!$schemaList instanceof SchemaList)
            {
                throw new BusinessExceptionInvalidArgument
                (
                	'Attempt to add non SchemaList object to Schema.'
                );
                return false;
            }
            else
            {
                $this->schemaList = $schemaList;
            }
        }
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
     * Schemas and returns 1 or 0 depending on
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
        $compare = strnatcasecmp
        (
            $a->title
            ,$b->title
        );

        return $compare;
    }

}
