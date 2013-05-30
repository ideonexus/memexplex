<?php

/**
 * Handles values submitted from an HTML form. Form will appear HTML screen
 * where pagecode matches the name of this class ProcessFormPAGECODE
 *
 * @package MemexPlex
 * @subpackage Business
 * @see ProcessForm
 * @see ProcessFormInterface
 * @author Kate Libby
 */

class ProcessFormSchema extends ProcessForm
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

    	//FLAG TO DETECT CHANGES
        $deltaExists = false;

        //INSTANTIATE OBJECT TO ENCAPSULATE DATA TO SUBMIT
        $deltaList = new DeltaList();

        //formValuesArray IS SET IN processForm.php
        if ($this->formValuesArray["schemaId"] != '')
        {
            $schemaId = $this->formValuesArray["schemaId"];
            
            //Check Edit Privileges
            $dac = new DataAccessComponentCurator;
            $curator = $dac->getCuratorByObjectId(null,null,$schemaId,null);
            if (!CuratorSession::checkEditPrivileges($curator->getId()))
            {
                ErrorCollection::addUserErrorMessage(0, "Insufficient privileges.");
            }
            
            if ($this->formValuesArray["deletefunction"] == 'true')
            {
                $function = Delta::DELETE;
            }
            else
            {
                $function = Delta::UPDATE;
            }
        }
        else
        {
            $function = Delta::INSERT;
        }

        try
        {
        	  if (trim($this->formValuesArray["schemaTitle"]) == "")
        	  {
        	  	  ErrorCollection::addUserErrorMessage("schemaTitle", "Title is required.");
        	  }
        	  
            $taxonomyList = new TaxonomyList;
            foreach (explode(",",$this->formValuesArray["schemaTaxonomies"]) as $taxonomy)
            {
                $taxonomyList[] = new Taxonomy(0,$taxonomy);
            }

            $purifier = HtmlValidation::getHtmlPurifier();
            $schemaDescription = $purifier->purify($this->formValuesArray["schemaDescription"]);
            
						if (mb_strlen($schemaDescription) > 10000)
						{
								ErrorCollection::addUserErrorMessage("schemaDescription", "Description is too long (".strlen($schemaDescription)."/10000 chars)");
					  }
						
            $datePublished = $this->formValuesArray["datepublished"];
            if ((($function == Delta::UPDATE
                && $this->formValuesArray["originaldisseminate"] != 'Y')
                || $function == Delta::INSERT)
                && $this->formValuesArray["disseminate"] == 'Y'
            )
            {
                $datePublished = date("Y-m-d");
            }
            
            //ADD DELTA OBJECT TO DELTALIST
            $deltaList[] =
                new Delta
                (
                    new Schema
                    (
                        $this->formValuesArray["schemaId"]
                        ,trim($this->formValuesArray["schemaTitle"])
                        ,CuratorSession::getCuratorFromSession()
                        ,($this->formValuesArray["disseminate"] == 'Y') ? 1 : 0 //$published     = null
                        ,$datePublished //$datePublished = null
                        ,null //$date          = null
                        ,$taxonomyList

                        ,$schemaDescription
                        ,null //$memeList
                        ,null //$schemaList
                    )
                    ,$function
                );
            $deltaExists = true;
        }
        catch (BusinessException $e)
        {
            ErrorCollection::addUserErrorMessage(0, $e);
        }

//DEBUGGING STATEMENTS FOR DEVELOPERS, UNCOMMENT LINES TO SEE ARRAYS OF VALUES BEING PROCESSED
//ErrorCollection::addUserErrorMessage(0, "formValuesArray:" . print_r($this->formValuesArray));
//ErrorCollection::addUserErrorMessage(0, "schemaDeltaList:" . print_r($deltaList));

    	//SUCCESS/ERROR FLAG
        $success = '';

        // SEND OBJECTS TO BUSINESS LAYER IF NO ERRORS AND DELTAS EXIST
        if (ErrorCollection::hasUserErrorMessages() == false && $deltaExists)
        {
            try
            {
                $bc = new DataAccessComponentSchema();
                $schemaId = $bc->saveSchemaList($deltaList);
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

            //IF SUCCESSFUL, SET SUCCESS FLAG FOR PRESENTATION LAYER
            if (false == ErrorCollection::hasUserErrorMessages())
            {
                //If Schema is being disseminated, publish all of its memes.
                if ($function == Delta::UPDATE
                    && $this->formValuesArray["disseminate"] == 'Y'
                    && $this->formValuesArray["originaldisseminate"] != 'Y')
                {
                    try
                    {
                        $bc = new DataAccessComponentMeme();
                        $bc->publishAllSchemaMemes($this->formValuesArray["schemaId"]);
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
                
                $functionText = "updated";
                if ($function == Delta::DELETE)
                {
                    $functionText = "deleted";
                }
                elseif ($function == Delta::INSERT)
                {
                    $functionText = "added";
                }
                ErrorCollection::addUserSuccessMessage(0, "Schema {$functionText}.");
                $success = "SUCCESS::";
            }
        }

        if
        (
            ErrorCollection::hasUserErrorMessages() == false
            && $function == Delta::INSERT
            &&
            (
                isset($this->formValuesArray["memeid"])
                && $this->formValuesArray["memeid"] != ''
            )
        )
        {
            try
            {
                $bc = new DataAccessComponentEntityRelationship();
                $bc->saveSchemaMeme($schemaId,$this->formValuesArray["memeid"]);
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

        if
        (
            ErrorCollection::hasUserErrorMessages() == false
            && $function == Delta::INSERT
            &&
            (
                isset($this->formValuesArray["parentschemaid"])
                && $this->formValuesArray["parentschemaid"] != ''
            )
        )
        {
            try
            {
                $bc = new DataAccessComponentEntityRelationship();
                $bc->saveSchemaParentChild($this->formValuesArray["parentschemaid"],$schemaId);
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

        //IF NO DELTAS, ADD AS ERROR MESSAGE
        if
        (
            ErrorCollection::hasUserErrorMessages() == false
            && $deltaExists == false
        )
        {
            ErrorCollection::addUserErrorMessage(0, "No modifications to Schema.");
        }

        //IF NOT SUCCESSFUL SET ERROR FLAG FOR PRESENTATION LAYER
        if (ErrorCollection::hasUserErrorMessages())
        {
            $success = "ERROR::";
        }

        // FORMAT MESSAGES FOR PRESENTATION
        $errors = new ErrorDisplay();
        if ($success == 'SUCCESS::'
            && ($function == Delta::INSERT || $function == Delta::DELETE)
            && get_class($this) != 'ProcessFormSchemaModal')
        {
            $success = 'REDIRECT::';
            $url = ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS');
            if ($schemaId && $function == Delta::INSERT)
            {
                $url .= "Schema/".$schemaId."/";
            }
            elseif($function == Delta::DELETE)
            {
                $url .= "SchemaList/";
            }
            $errors->buildRedirect($url);
        }
        else
        {
            $errors->buildErrorsHtmlDisplay(true);
        }

        return $success . $errors->getSource();
    }

}
