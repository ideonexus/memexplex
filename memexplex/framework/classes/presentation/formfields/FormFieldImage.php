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
class FormFieldImage extends FormField
implements FormFieldInterface
{

    /**
     * @var string Html HEIGHT attribute.
     */
    protected $height;

    /**
     * @param string $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @var string Html WIDTH attribute.
     */
    protected $width;

    /**
     * @param string $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * Default value and source are the same.
     */
    public function setSource()
    {
        if ($this->defaultValue != "")
        {
            $this->source = "<img"
                            ." src=\"{$this->defaultValue}\""
                            ." height=\"{$this->height}\""
                            ." width=\"{$this->width}\""
                            ." />";
        }
        else
        {
            $this->source = "";
        }
        $this->defaultValue = $this->source;
    }

}
