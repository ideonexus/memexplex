<?php
/**
 * Taxonomies are like metatags, a folksonomy, where users generate the tags
 * they think appropriate as they go; as opposed to an ontology, where the
 * application designates what tags are allowed and what they mean (@see Predicate).
 * The folksonomy strategy for taxonomies makes them more useful to the Curator
 * who owns the object at the expense of their usefulness to others in the 
 * community. 
 *
 * @package MemexPlex
 * @subpackage Business.Entity
 * @author Friday
 */
class Taxonomy
{

    /**
     * @var integer Database key. Taxonomies are unique so we may group them 
     * semantically at a later point.
     */
    protected $id;

	/**
     * @var string Text of the taxonomy, lowercase, may be multiple words.
     */
    protected $text;

    /**
     * Called when a new object is instantiated, 
     * accepts all properties as arguments.
     *
     * @param integer $id
     * @param string $text
     */
    public function __construct(
        $id       = null
        ,$text    = null
    )
    {
        $this->setId($id);
        $this->setText($text);
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
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string
     */
    public function setText($text="")
    {
        $this->text = $text;
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
            $a->text
            ,$b->text
        );

        return $compare;
    }

}
