<?php

/**
 * Handles values submitted from an HTML form. Form will appear HTML screen
 * where pagecode matches the name of this class ProcessFormPAGECODE
 *
 * @package MemexPlex
 * @subpackage Business
 * @see ProcessForm
 * @see ProcessFormInterface
 * @author Tony Stark
 */

class ProcessFormTriple extends ProcessForm
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
        if ($this->formValuesArray["tripleId"] != '')
        {
            $tripleId = $this->formValuesArray["tripleId"];
            
            //Check Edit Privileges
            $dac = new DataAccessComponentCurator;
            $curator = $dac->getCuratorByObjectId(null,null,null,$tripleId);
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

            $taxonomyList = new TaxonomyList;
            foreach (explode(",",$this->formValuesArray["tripleTaxonomies"]) as $taxonomy)
            {
                $taxonomyList[] = new Taxonomy(0,$taxonomy);
            }

            $purifier = HtmlValidation::getHtmlPurifier();
            $tripleDescription = $purifier->purify($this->formValuesArray["tripleDescription"]);
            
						if (mb_strlen($tripleDescription) > 10000)
						{
								ErrorCollection::addUserErrorMessage("tripleDescription", "Description is too long (".strlen($tripleDescription)."/10000 chars)");
					  }
						
            if (trim($this->formValuesArray["tripleTitle"]) == "")
            {
                ErrorCollection::addUserErrorMessage(
                    "tripleTitle"
                    ,"Please enter a Title.");
            }

            if ($this->formValuesArray["predicateid"] == "")
            {
                ErrorCollection::addUserErrorMessage(
                    "predicateid"
                    ,"Please select a Predicate.");
            }

            if ($this->formValuesArray["subjectId"] == "")
            {
                ErrorCollection::addUserErrorMessage(
                    0
                    ,"Please select a Subject Meme.");
            }

            if ($this->formValuesArray["objectId"] == "")
            {
                ErrorCollection::addUserErrorMessage(
                    0
                    ,"Please select a Object Meme.");
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
                    new Triple
                    (
                        $this->formValuesArray["tripleId"]
                        ,trim($this->formValuesArray["tripleTitle"])
                        ,CuratorSession::getCuratorFromSession()
                        ,($this->formValuesArray["disseminate"] == 'Y') ? 1 : 0 //$published     = null
                        ,$datePublished //$datePublished = null
                        ,null           //$date          = null
                        ,$taxonomyList

                        ,$tripleDescription
                        ,new Meme($this->formValuesArray["subjectId"])        //subjectMeme   = null
                        ,new Predicate($this->formValuesArray["predicateid"]) //predicate     = null
                        ,new Meme($this->formValuesArray["objectId"])         //objectMeme    = null
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
//ErrorCollection::addUserErrorMessage(0, "tripleDeltaList:" . print_r($deltaList));

    	//SUCCESS/ERROR FLAG
        $success = '';

        // SEND OBJECTS TO BUSINESS LAYER IF NO ERRORS AND DELTAS EXIST
        if (ErrorCollection::hasUserErrorMessages() == false && $deltaExists)
        {
            try
            {
                $bc = new DataAccessComponentTriple();
                $tripleId = $bc->saveTripleList($deltaList);
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
                ErrorCollection::addUserSuccessMessage(0, "Triple {$functionText}.");
                $success = "SUCCESS::";
            }
        }

        //IF NO DELTAS, ADD AS ERROR MESSAGE
        if
        (
            ErrorCollection::hasUserErrorMessages() == false
            && $deltaExists == false
        )
        {
            ErrorCollection::addUserErrorMessage(0, "No modifications to Triple.");
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
            && get_class($this) != 'ProcessFormTripleModal')
        {
            $success = 'REDIRECT::';
            $url = ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS');
            if ($tripleId && $function == Delta::INSERT)
            {
                $url .= "Triple/".$tripleId."/";
            }
            elseif($function == Delta::DELETE)
            {
                $url .= "TripleList/";
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
