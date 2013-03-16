<?php
/**
 * Authors go with references. I'm breaking them out because I'll be able to
 * get more functionality out of them later if I do so now.
 *
 * @package MemexPlex
 * @subpackage Business.Entity
 * @author Dr. Yes
 */
class Author
{

    /**
     * @var integer Database key for the Author
     */
    protected $id;

	/**
     * @var string First name.
     */
    protected $firstName;

	/**
     * @var string Last name.
     */
    protected $lastName;

    /**
     * Called when a new object is instantiated, 
     * accepts all properties as arguments.
     *
     * @param integer $id
     * @param string $text
     */
    public function __construct(
        $id         = null
        ,$firstName = null
        ,$lastName  = null
    )
    {
        $this->setId($id);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer
     */
    public function setId($id="")
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string
     */
    public function setFirstName($firstName="")
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string
     */
    public function setLastName($lastName="")
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->lastName.", ".$this->firstName;
    }

    /**
     * This function compares the text of two
     * Taxonomies and returns 1 or 0 depending on
     * if they are in alphabetical order or not.
     * It is used by the TaxonomyList to sort.
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
            $a->lastName
            ,$b->lastName
        );

        return $compare;
    }

}
