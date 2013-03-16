<?php

/**
 * Handles values submitted from an HTML form. Form will appear HTML screen
 * where pagecode matches the name of this class ProcessFormPAGECODE
 *
 * @package MemexPlex
 * @subpackage Business
 * @see ProcessForm
 * @see ProcessFormInterface
 * @author Dr. Edward "Eddie" Jessup
 */

class ProcessFormReference extends ProcessForm
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
        if ($this->formValuesArray["referenceId"] != '')
        {
            $referenceId = $this->formValuesArray["referenceId"];
            
            //Check Edit Privileges
            $dac = new DataAccessComponentCurator;
            $curator = $dac->getCuratorByObjectId(null,$referenceId,null,null);
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
            $authorList = new AuthorList;
            for ($i=0;$i<($this->formValuesArray['hidAuthorsTableEditRowCount']+1);$i++)
            {
                $authorList[] = new Author(
                    $this->formValuesArray['authorId'.$i]
                    ,$this->formValuesArray['authorFirstName'.$i]
                    ,$this->formValuesArray['authorLastName'.$i]
                );
            }
            
            $taxonomyList = new TaxonomyList;
            foreach (explode(",",$this->formValuesArray["referenceTaxonomies"]) as $taxonomy)
            {
                $taxonomyList[] = new Taxonomy(0,$taxonomy);
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
                    new Reference
                    (
                        $this->formValuesArray["referenceId"]
                        ,$this->formValuesArray["referenceTitle"]
                        ,CuratorSession::getCuratorFromSession()
                        ,($this->formValuesArray["disseminate"] == 'Y') ? 1 : 0 //$published
                        ,$datePublished //$datePublished
                        ,null           //$date
                        ,$taxonomyList

                        ,new ReferenceSuperType()
                        ,new ReferenceType
                    	(
                    	    $this->formValuesArray["referenceType"]
                    	    ,''
                    	)
                        ,$authorList
                        ,$this->formValuesArray["referenceDate"]
                        ,$this->formValuesArray["referencePublicationLocation"]
                        ,$this->formValuesArray["referencePublisherPeriodical"]
                        ,$this->formValuesArray["referenceVolumePages"]
                        ,$this->formValuesArray["referenceUrl"]
                        ,$this->formValuesArray["referenceService"]
                        ,$this->formValuesArray["referenceDateRetrieved"]
                        ,$this->formValuesArray["referenceIsbn"]
                        ,$this->formValuesArray["referenceEan"]
                        ,$this->formValuesArray["referenceUpc"]
                        ,$this->formValuesArray["referenceSmallImageUrl"]
                        ,$this->formValuesArray["referenceLargeImageUrl"]
                        ,$this->formValuesArray["asin"]
                        ,$this->formValuesArray["amazonurl"]
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
//ErrorCollection::addUserErrorMessage(0, "referenceDeltaList:" . print_r($deltaList));

    	//SUCCESS/ERROR FLAG
        $success = '';

        // SEND OBJECTS TO BUSINESS LAYER IF NO ERRORS AND DELTAS EXIST
        if (ErrorCollection::hasUserErrorMessages() == false && $deltaExists)
        {
            try
            {
                $bc = new DataAccessComponentReference();
                $referenceId = $bc->saveReferenceList($deltaList);
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
                $functionText = "updated";
                if ($function == Delta::DELETE)
                {
                    $functionText = "deleted";
                }
                elseif ($function == Delta::INSERT)
                {
                    $functionText = "added";
                }
                ErrorCollection::addUserSuccessMessage(0, "Reference {$functionText}.");
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
                $bc->saveMemeReference($referenceId,$this->formValuesArray["memeid"]);
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
                isset($this->formValuesArray["parentreferenceid"])
                && $this->formValuesArray["parentreferenceid"] != ''
            )
        )
        {
            try
            {
                $bc = new DataAccessComponentEntityRelationship();
                $bc->saveReferenceParentChild($this->formValuesArray["parentreferenceid"],$referenceId);
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
            ErrorCollection::addUserErrorMessage(0, "No modifications to Reference.");
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
            && get_class($this) != 'ProcessFormReferenceModal')
        {
            $success = 'REDIRECT::';
            $url = ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS');
            if ($referenceId && $function == Delta::INSERT)
            {
                $url .= "Reference/".$referenceId."/";
            }
            elseif($function == Delta::DELETE)
            {
                $url .= "ReferenceList/";
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
