<?php

/**
 * Builds HTML code for presentation to the client.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 */
abstract class Html
{

    /**
     * @var string - Optional source code obtained from another Html Object.
     */
    protected $externalSource='';

    /**
     * @var string - The HTML source code.
     */
    protected $source;

    /**
     * Accepts and optional Html object as an argument,
     * which the setSource() method will append to the appropriate
     * place in the html string.
     *
     * @var Html - Optional Html Object.
     */
    public function __construct($html=null)
    {
        if ($html)
        {
            if ($html instanceof Html)
            {
                $html->setSource();
                $this->externalSource = $html->getSource();
                //DEFAULT SOURCE TO EXTERNALSOURCE
                //IF SOURCE NOT SET IN CURRENT OBJECT
                $this->source = $this->externalSource;
            }
            else
            {
                throw new PresentationExceptionConfigurationError
                (
                    'Non Html object submitted to ' . get_class($this)
                );
                return false;
            }
        }
    }

    /**
     * Sets the source code for the html string.
     * [TODO] Make this abstract (means refactoring some Html classes.
     */
    //abstract public function setSource();
    public function setSource()
    {
        //empty
    }

    /**
     * Returns the html source code.
     *
     * @return string - HTML source code.
     */
    public function getSource()
    {
        return $this->source;
    }

}
