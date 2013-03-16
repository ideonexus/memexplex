<?php

/**
 * Processes an array of POST variables submitted from a form.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 */
abstract class ProcessForm
{

    /**
     * @var array An array of POST values from the form.
     */
    protected $formValuesArray;

    /**
     * An array of POST values from the form.
     *
     * @param array $formValuesArray <todo:description>
     */
    public function setValuesArray($formValuesArray = array())
    {
        $this->formValuesArray = $formValuesArray;
    }

    /**
     * Determins the type of delta.
     *
     * @param mixed $originalValue <todo:description>
     * @param mixed $newValue <todo:description>
     * @return mixed <todo:description>
     */
    protected function determineOperation
    (
        $originalValue = '',
        $newValue = ''
    )
    {

        //(I)NSERT, (U)PDATE, (D)ELETE
        if
        (
            $originalValue == '' &&
            $newValue != ''
        )
        {
            return Delta::INSERT;
        }
        else if
        (
            $newValue == '' &&
            $originalValue != ''
        )
        {
            return Delta::DELETE;
        }
        else if
        (
            $newValue      != '' &&
            $originalValue != '' &&
            $newValue      != $originalValue
        )
        {
            return Delta::UPDATE;
        }
        else
        {
            return false;
        }

    }

    /**
     * Processes a form HTML id value, validates it, and returns the type of 
     * delta being performed on it.
     *
     * @param array $validationList <todo:description>
     * @param string $dataType <todo:description>
     * @param mixed $entryValue <todo:description>
     * @param int $entryId <todo:description>
     * @param string $entryLabel <todo:description>
     */
    protected function validateEntry
    (
        $validationList,
        $dataType,
        $entryValue,
        $entryId,
        $entryLabel = ''
    )
    {
        // VALIDATE ENTRY
        foreach ($validationList as $validation)
        {
            $validationClassName    = $dataType . "Validation";
            $validationFunctionName = (string) $validation;

            $result = call_user_func
            (
                array($validationClassName, $validationFunctionName),
                $entryValue,
                $entryLabel
            );

            if ($result[0] != true)
            {
                ErrorCollection::addUserErrorMessage($entryId, $result[1]);
            }

            unset($result);
        }
    }

    /**
     * Validates a form entry.
     *
     * @param object $formfield HTML input id.
     * @param string $modifier Modifier to the formfield id, like a rownumber.
     * @return bool <todo:description>
     */
    protected function processEntry($formfield, $modifier = '')
    {
        if (isset($this->formValuesArray[(string) $formfield->id . $modifier]))
        {
            $originalValue = "";
            if (isset($this->formValuesArray[(string) 'original' . $formfield->id . $modifier]))
            {
                $originalValue = $this->formValuesArray[(string) 'original' . $formfield->id . $modifier];
            }

            if ($this->formValuesArray[(string) $formfield->id . $modifier] != $originalValue)
            {
                // DETERMINE TYPE OF OPERATION
                $flag = $this->determineOperation
                (
                    $originalValue
                    ,$this->formValuesArray[(string) $formfield->id . $modifier]
                );

                if ($flag != false)
                {
                    //VALIDATE ENTRY
                    $this->validateEntry
                    (
                        $formfield->validation->validate
                        ,(string) $formfield->type
                        ,$this->formValuesArray[(string) $formfield->id . $modifier]
                        ,(string) $formfield->id
                        ,(string) $formfield->label
                    );
                    return $flag;
                }
                else
                {
                    return false;
                }
            }
        }
    }
}
