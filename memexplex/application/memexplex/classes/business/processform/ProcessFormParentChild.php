<?php

/**
 * Handles values submitted from an HTML form. Form will appear HTML screen
 * where pagecode matches the name of this class ProcessFormPAGECODE
 *
 * @package MemexPlex
 * @subpackage Business
 * @see ProcessForm
 * @see ProcessFormInterface
 * @author Dr. David Bowman
 */
class ProcessFormParentChild extends ProcessForm
implements ProcessFormInterface
{

    /**
     * Accepts the values of the form, validates them, assembles the
     * appropriate business entities, and sends them along to the appropriate DAC.
     *
     * @return string Errors/Success messages for the UI.
     */
    public function process()
    {

//DEBUGGING STATEMENTS FOR DEVELOPERS, UNCOMMENT LINES TO SEE ARRAYS OF VALUES BEING PROCESSED
//ErrorCollection::addUserErrorMessage(0, "formValuesArray:" . print_r($this->formValuesArray));

    	//SUCCESS/ERROR FLAG
        $success = '';
        $associaionType = "";

        if (isset($this->formValuesArray["referenceid"])
            && $this->formValuesArray["referenceid"] != ''
            && isset($this->formValuesArray["memeid"])
            && $this->formValuesArray["memeid"] != '')
        {
            try
            {
                $associaionType = "Reference-Meme";
                $bc = new DataAccessComponentEntityRelationship();
                if ($this->formValuesArray["function"] == 'associate')
                {
                    $bc->saveMemeReference(
                        $this->formValuesArray["referenceid"]
                        ,$this->formValuesArray["memeid"]
                    );
                }
                elseif ($this->formValuesArray["function"] == 'disassociate')
                {
                    $bc->deleteMemeReference(
                        $this->formValuesArray["referenceid"]
                        ,$this->formValuesArray["memeid"]
                    );
                }
            }
            //IF UNEXPECTED ERRORS, SEND TO HANDLER TO
            //PROPERLY FORMAT FOR THE PRESENTATION LAYER
            catch (GeneralException $e)
            {
                ErrorCollection::addUserErrorMessage
                (
                    0
                    ,ErrorDisplay::unexpectedExceptionDisplay($e)
                );
            }
        }

        if (isset($this->formValuesArray["parentreferenceid"])
            && $this->formValuesArray["parentreferenceid"] != ''
            && isset($this->formValuesArray["referenceid"])
            && $this->formValuesArray["referenceid"] != '')
        {
            try
            {
                $associaionType = "Reference-Reference";
                $bc = new DataAccessComponentEntityRelationship();
                if ($this->formValuesArray["function"] == 'associate')
                {
                    $bc->saveReferenceParentChild(
                        $this->formValuesArray["parentreferenceid"]
                        ,$this->formValuesArray["referenceid"]
                    );
                }
                elseif ($this->formValuesArray["function"] == 'disassociate')
                {
                    $bc->deleteReferenceParentChild(
                        $this->formValuesArray["parentreferenceid"]
                        ,$this->formValuesArray["referenceid"]
                    );
                }
            }
            //IF UNEXPECTED ERRORS, SEND TO HANDLER TO
            //PROPERLY FORMAT FOR THE PRESENTATION LAYER
            catch (GeneralException $e)
            {
                ErrorCollection::addUserErrorMessage
                (
                    0
                    ,ErrorDisplay::unexpectedExceptionDisplay($e)
                );
            }
        }

        if (isset($this->formValuesArray["schemaid"])
            && $this->formValuesArray["schemaid"] != ''
            && isset($this->formValuesArray["memeid"])
            && $this->formValuesArray["memeid"] != '')
        {
            try
            {
                $associaionType = "Schema-Meme";
                $bc = new DataAccessComponentEntityRelationship();
                if ($this->formValuesArray["function"] == 'associate')
                {
                    $bc->saveSchemaMeme(
                        $this->formValuesArray["schemaid"]
                        ,$this->formValuesArray["memeid"]
                    );
                }
                elseif ($this->formValuesArray["function"] == 'disassociate')
                {
                    $bc->deleteSchemaMeme(
                        $this->formValuesArray["schemaid"]
                        ,$this->formValuesArray["memeid"]
                    );
                }
            }
            //IF UNEXPECTED ERRORS, SEND TO HANDLER TO
            //PROPERLY FORMAT FOR THE PRESENTATION LAYER
            catch (GeneralException $e)
            {
                ErrorCollection::addUserErrorMessage
                (
                    0
                    ,ErrorDisplay::unexpectedExceptionDisplay($e)
                );
            }
        }

        if (isset($this->formValuesArray["parentschemaid"])
            && $this->formValuesArray["parentschemaid"] != ''
            && isset($this->formValuesArray["schemaid"])
            && $this->formValuesArray["schemaid"] != '')
        {
            try
            {
                $associaionType = "Schema-Meme";
                $bc = new DataAccessComponentEntityRelationship();
                if ($this->formValuesArray["function"] == 'associate')
                {
                    $bc->saveSchemaParentChild(
                        $this->formValuesArray["parentschemaid"]
                        ,$this->formValuesArray["schemaid"]
                    );
                }
                elseif ($this->formValuesArray["function"] == 'disassociate')
                {
                    $bc->deleteSchemaParentChild(
                        $this->formValuesArray["parentschemaid"]
                        ,$this->formValuesArray["schemaid"]
                    );
                }
            }
            //IF UNEXPECTED ERRORS, SEND TO HANDLER TO
            //PROPERLY FORMAT FOR THE PRESENTATION LAYER
            catch (GeneralException $e)
            {
                ErrorCollection::addUserErrorMessage
                (
                    0
                    ,ErrorDisplay::unexpectedExceptionDisplay($e)
                );
            }
        }

        //IF NOT SUCCESSFUL SET ERROR FLAG FOR PRESENTATION LAYER
        if (ErrorCollection::hasUserErrorMessages())
        {
            $success = "ERROR::";
        }

        if (ErrorCollection::hasUserErrorMessages() == false)
        {
            if ($this->formValuesArray["function"] == 'disassociate')
            {
                ErrorCollection::addUserSuccessMessage(0, $associaionType." Association removed.");
            }
            else if ($this->formValuesArray["function"] == 'associate')
            {
                ErrorCollection::addUserSuccessMessage(0, $associaionType." Association established.");
            }
            $success = "SUCCESS::";
        }

        // FORMAT MESSAGES FOR PRESENTATION
        $errors = new ErrorDisplay();
        $errors->buildErrorsHtmlDisplay(true);

        return $success . $errors->getSource();
    }

}
