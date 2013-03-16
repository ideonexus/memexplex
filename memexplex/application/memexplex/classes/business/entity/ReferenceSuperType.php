<?php
/**
 * These are types of media like books, film, audio, etc.
 *
 * @package MemexPlex
 * @subpackage Business.Entity
 * @author Prometheus
 */
class ReferenceSuperType
{

    /**
     * @var integer Database Id.
     */
    protected $id;

	/**
     * @var string Description semantically meaningful to a human.
     */
    protected $description;

    /**
     * Called when a new object is instantiated, 
     * accepts all properties as arguments.
     *
     * @param string $id
     * @param string $description
     */
    public function __construct
    (
        $id           = null
    	,$description = null
    )
    {
        $this->id          = $id;
    	$this->description = $description;
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
     * ReferenceSuperTypes and returns 1 or 0 depending on
     * if they are in alphabetical order or not.
     * It is used by the ReferenceSuperTypeList to sort.
     *
     * @param FooBar $a FooBar a
     * @param FooBar $b FooBar b
     * @return int      1 or 0 for true/false
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
            $a->description
            ,$b->description
        );

        return $compare;
    }

}
