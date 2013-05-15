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
class FormFieldImageGoogleBooks extends FormField
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
     * If no image, sets an empty span tag equal in size
     * of the image with a message to add an image to the form.
     */
    public function setSource()
    {
        //Show Link to Google
        $this->source = 
            '<div'
        	.' class="googleBookDisplay">'
        	.'<a class="'.SUBMODAL_CLASS.'"'
        	.' href="'
            .ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
            .'GoogleBooksSearch/'
            .'" id="'.$this->id.'">';
            
        if ($this->defaultValue != "")
        {
            //Show the Amazon Image
            $this->source .= 
            	'<img'
                .' src="'.$this->defaultValue.'"'
                .' class="googleBookDisplay">'
                .' />';
            
            //Default value and source are the same.
            $this->defaultValue = $this->source;
        }
        else
        {
            //Show Link to Amazon
            $this->source .=
                '<span class="googleBookDisplayText">' 
                .'Click here to search Google Books for References'
                .'</span>';
        }
        
        //Show the Amazon Image
        $this->source .= 
        	'</a>'
            .'</div>';
    }

}
