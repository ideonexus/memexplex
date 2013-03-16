<?php
/**
 * This file contains the functionality for FooBar
 * business entities.
 *
 * @package MemexPlex
 * @subpackage Business.Entity
 * @author Winston Smith
 */
class ReferenceType
{

    /**
     * @var integer Database id.
     */
    protected $id;

	/**
     * @var ReferenceSuperTypeId Database id for the reference super type.
     */
    protected $referenceSuperTypeId;

    /**
     * @var string Meat-friendly description.
     */
    protected $description;

    /**
     * Called when a new object is instantiated, 
     * accepts all properties as arguments.
     *
     * @param integer $id
     * @param integer $referenceSuperTypeId
     * @param string $description
     */
    public function __construct
    (
        $id                    = null
    	,$referenceSuperTypeId = null
        ,$description          = null
    )
    {
        $this->id                   = $id;
    	$this->referenceSuperTypeId = $referenceSuperTypeId;
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
     * @return integer
     */
    public function getReferenceSuperTypeId()
    {
        return $this->referenceSuperTypeId;
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
     * ReferenceTypes and returns 1 or 0 depending on
     * if they are in alphabetical order or not.
     * It is used by the ReferenceTypeList to sort.
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
            $a->description
            ,$b->description
        );

        return $compare;
    }

}
