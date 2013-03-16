<?php

/**
 * This object encapsulates the common properties and methods of Memes, 
 * References, Schemas, Triples, and whatever else we decide to extend from it.
 *
 * @package MemexPlex
 * @subpackage Business.Entity
 * @author Egon Spengler
 */
abstract class MemexPlexObject
{

    /**
     * @var integer Database key.
     */
    protected $id;

	/**
     * @var string Title of the Meme, Reference, Schema, etc.
     */
    protected $title;

    /**
     * @var Curator Owner of the object.
     */
    protected $curator;

    /**
     * @var boolean Whether the object is available publicly or not.
     */
    protected $published=0;

    /**
     * @var date Date object was made public.
     */
    protected $datePublished;

    /**
     * @var date Last modified date.
     */
    protected $date;

    /**
     * @var TaxonomyList Metatags for the object.
     */
    protected $taxonomyList;

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
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
    	if (strtolower(substr($this->title,-5)) == ", the")
    	{
    	    $title = "The " . substr($this->title,0,-5);
    	}
    	elseif (strtolower(substr($this->title,-3)) == ", a")
    	{
    	    $title = "A " . substr($this->title,0,-3);
    	}
    	else
    	{
    	    $title = $this->title;
    	}
        return $title;
    }

    /**
     * @param string
     */
    public function setTitle($title)
    {
        $this->title = strip_tags($title, "<em>");
    	if (strtolower(substr($this->title,0,4)) == "the ")
    	{
    	    $this->title = substr($this->title,4) . ", The";
    	}
    	elseif (strtolower(substr($this->title,0,2)) == "a ")
    	{
    	    $this->title = substr($this->title,2) . ", A";
    	}
    }
    
    /**
     * @return Curator
     */
    public function getCurator()
    {
        return $this->curator;
    }
    
    /**
     * @param Curator
     */
    public function setCurator($curator=null)
    {
        if ($curator)
        {
            if (!$curator instanceof Curator)
            {
                throw new BusinessExceptionInvalidArgument
                (
                	'Attempt to add non Curator to MemexPlex object.'
                );
                return false;
            }
            else
            {
                $this->curator = $curator;
            }
        }
    }

    /**
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }
    
    /**
     * @param boolean
     */
    public function setPublished($published=0)
    {
        $this->published = $published;
    }

    /**
     * @return date
     */
    public function getDatePublished()
    {
        return $this->datePublished;
    }
    
    /**
     * @param date
     */
    public function setDatePublished($datePublished=null)
    {
        $this->datePublished = $datePublished;
    }

    /**
     * @return date
     */
    public function getDate()
    {
        return $this->date;
    }
    
    /**
     * @param date
     */
    public function setDate($date=null)
    {
        if ($date)
        {
            $this->date = date("Y-m-d H:i:s",$date);
        }
        else
        {
            $this->date = date("Y-m-d H:i:s",time());
        }
    }
    
    /**
     * @return TaxonomyList
     */
    public function getTaxonomyList()
    {
        return $this->taxonomyList;
    }

    /**
     * @param TaxonomyList
     */
    public function setTaxonomyList($taxonomyList=null)
    {
        if ($taxonomyList)
        {
            if (!$taxonomyList instanceof TaxonomyList)
            {
                throw new BusinessExceptionInvalidArgument
                (
                    'Attempt to add non TaxonomyList object to Meme.'
                );
                return false;
            }
            else
            {
                $this->taxonomyList = $taxonomyList;
            }
        }
    }

}
