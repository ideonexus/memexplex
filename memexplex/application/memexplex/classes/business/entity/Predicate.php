<?php
/**
 * Predicates are used in triples to define the semantic association between
 * two memes, a subject and object. Some relationships are for memes to reinforce,
 * contrast, or summarize one another.  
 * 
 * Predicates are an ontology, the application restricts users to only those 
 * predicates defined in the database. This will be useful for filtering triples
 * by the kind of relationship each meme has to each other.
 *
 * @package MemexPlex
 * @subpackage Business.Entity
 * @author Kitty Pryde
 */
class Predicate
{

    /**
     * @var integer Database key for the predicate.
     */
    protected $id;

    /**
     * @var Predicate Human semantic key for the predicate.
     */
    protected $description;

    /**
     * Called when a new object is instantiated, 
     * accepts all properties as arguments.
     *
     * @param integer $id
     * @param string  $description
     */
    public function __construct(
        $id                    = null
        ,$description          = null
    )
    {
        $this->id                   = $id;
        $this->description          = $description;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * This function compares the descriptions of two
     * Predicates and returns 1 or 0 depending on
     * if they are in alphabetical order or not.
     * It is used by the PredicateList to sort by name.
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
            $a->description
            ,$b->description
        );

        return $compare;
    }

}
