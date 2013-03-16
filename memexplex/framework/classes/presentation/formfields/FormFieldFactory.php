<?php

/**
 * Spits out FormField objects. Really this is a SimpleFactory.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 */
class FormFieldFactory
{
    /**
     * Creates a new FormField object.
     *
     * @param string $formField Type of Formfield.
     * @return FormField
     */
    public static function create($formField)
    {
        $formFieldName = 'FormField' . $formField;
        return new $formFieldName;
    }

    /**
     * Sets the properties and methods of a FormField.
     *
     * @param FormField $formfield FormField Object to set.
     * @param SimpleXmlObject $rowdata Data for the row.
     * @param SimpleXmlObject $pagedata All Data for the page.
     * @param string $currentrow Modifier for Formfield ID.
     * @return FormField
     */
    public static function setFormfield($formfield,$rowdata,$pagedata,$currentrow=null)
    {
        $htmlFormField = self::create($formfield->type);
        $htmlFormField->setLabel($formfield->label);
        if ($currentrow)
        {
            $htmlFormField->setId($formfield->id . $currentrow);
        }
        else
        {
            $htmlFormField->setId($formfield->id);
        }
        $htmlFormField->setData($formfield, $rowdata, $pagedata);

        //LOOP THROUGH CUSTOM METHODS
        if (isset($formfield->methods))
        {
            foreach ($formfield->methods->children() as $method=>$input)
            {
                if ($currentrow)
                {
                    $htmlFormField->$method
                    (
                        str_replace
                        (
                             "%CURRENTROW%"
                             ,$currentrow
                             ,$input
                        )
                    );
                }
                else
                {
                    $htmlFormField->$method($input);
                }
            }
        }

        $htmlFormField->setDefaultValue
        (
            (string) SimpleXml::getSimpleXmlItem
            (
                $rowdata
                ,$formfield->valueXpath
            )
        );

        return $htmlFormField;
    }

}
