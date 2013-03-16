<?php

/**
 * Builds an image.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormField
 * @see FormFieldInterface
 */
class FormFieldMemexPlexObjectCount extends FormField
implements FormFieldInterface
{

    /**
     * @var string Html WIDTH attribute.
     */
    protected $objectType;

    /**
     * @param string $width
     */
    public function setObjectType($objectType)
    {
        $this->objectType = $objectType;
    }

    /**
     * Default value and source are the same.
     */
    public function setSource()
    {
        if ($this->defaultValue != "" && $this->defaultValue != "0")
        {
            $this->source = $this->defaultValue
            				."&nbsp;<img"
                            ." src=\"".ROOT_FOLDER."framework/images/{$this->objectType}_blue.gif\""
                            ." height=\"13\""
                            ." width=\"13\""
                            ." />&nbsp;";
        }
        else
        {
            $this->source = "";
        }
        $this->defaultValue = $this->source;
    }

}
