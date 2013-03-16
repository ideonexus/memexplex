<?php

/**
 * Curator Levels are like Slashdot Karma, only there are many more degrees.
 * The lowest level is blocked from the system, while the other levels are
 * based on the electromagnetic spectrum, which works like a metaphor for
 * the range of a Curator's influence. At the short-wavelength end of the
 * spectrum, we have radio waves, with a short range; at the long-wavelength
 * end, we have gamma rays, with a range that crosses the Universe.
 *
 * @package MemexPlex
 * @subpackage Business.Entity
 * @author Peter Parker
 */
class CuratorLevel
{

    /**
     * @var integer Database key for the level.
     */
    protected $id;

    /**
     * @var string Code representing the level.
     */
    protected $code;

    /**
     * @var string Level Description, 
     */
    protected $description;

    /**
     * Called when a new object is instantiated, 
     * accepts all properties as arguments.
     *
     * @param integer $id
     * @param string $code
     * @param string $description
     */
    public function __construct
    (
        $id              = null
    	,$code           = null
        ,$description    = null
    )
    {
        $this->id             = $id;
    	$this->code           = $code;
        $this->description    = $description;
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * This function compares the display names of two
     * Curators and returns 1 or 0 depending on
     * if they are in alphabetical order or not.
     * It is used by the CuratorList to sort by name.
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
            $a->id
            ,$b->id
        );

        return $compare;
    }

}
