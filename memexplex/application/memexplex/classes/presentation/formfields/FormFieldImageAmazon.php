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
class FormFieldImageAmazon extends FormField
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
        //Show Link to Amazon
        $this->source = 
            '<div'
        	.' style="width:'.$this->width.'px;height:'.$this->height.'px;'
        	.'float:right;border:solid;position:relative;top:-20px;'
        	.'text-align:center;margin-bottom:-20px;">'
        	.'<a class="'.SUBMODAL_CLASS.'"'
        	.' href="'
            .ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
            .'AmazonSearch/'
            .'" id="'.$this->id.'">';
            
        if ($this->defaultValue != "")
        {
            //Show the Amazon Image
            $this->source .= 
            	'<img'
                .' src="'.$this->defaultValue.'"'
                .' height="'.$this->height.'"'
                .' width="'.$this->width.'"'
                .' />';
            
            //Default value and source are the same.
            $this->defaultValue = $this->source;
        }
        else
        {
            //Show Link to Amazon
            $this->source .=
                '<br/><span style="font-size:150%;">' 
                .'Click here<br/>to search<br/>books, dvds,<br/>software<br/>and music for<br/>References'
                .'</span>';
        }
        
        //Show the Amazon Image
        $this->source .= 
        	'</a>'
            .'</div>';
    }

}
