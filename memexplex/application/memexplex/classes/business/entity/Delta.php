<?php

/**
 * Serves as a container class for entities being passed to the persistence
 * layer. For instance, when you want to insert a new Meme to the database,
 * package it in a new instance of this class with an "I" flag for INSERT and
 * send it as a parameter to the DAC->saveMemeList() function.
 *
 * @package MemexPlex
 * @subpackage Business.Entity
 * @author Duckie
 */
class Delta
{
    /**
     * Delta constants.
     */
    const INSERT = 'I';
    const UPDATE = 'U';
    const DELETE = 'D';

    /**
     * @var object The object being flagged for CRUD.
     */
    protected $object;

    /**
     * @var string I, U, or D as defined in the constants.
     */
    protected $flag;

    /**
     * Called when a new object is instantiated, 
     * accepts all properties as arguments.
     *
     * @param object $object
     * @param string $flag
     */
    public function __construct(
        $object       = null
        ,$flag        = null
    )
    {
        $this->object = $object;
        $this->setFlag($flag);
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }
    
    /**
     * @return string
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * @param string I, U, or D
     */
    public function setFlag($flag)
    {
        if (!self::validate($flag))
        {
            throw new BusinessExceptionInvalidDelta("Invalid flag submitted to Delta.");
        }

        $this->flag  = $flag;
    }

    /**
     * Validates the delta flag against the CRUD constants.
     *
     * @param string $flag
     * @return bool
     */
    final public static function validate($flag)
    {

        switch ($flag)
        {
            case self::INSERT:
                // fall through

            case self::UPDATE:
                // fall through

            case self::DELETE:
                return true;

            default:
                return false;
        }
    }
}
