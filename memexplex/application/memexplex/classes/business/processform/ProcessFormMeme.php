<?php

/**
 * Handles values submitted from an HTML form. Form will appear HTML screen
 * where pagecode matches the name of this class ProcessFormPAGECODE
 *
 * @package MemexPlex
 * @subpackage Business
 * @see ProcessForm
 * @see ProcessFormInterface
 * @author Arthur Frayn
 */
class ProcessFormMeme extends ProcessForm
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
        if ($this->formValuesArray["id"] != '' && $this->formValuesArray["id"] != '0')
        {
            $memeId = $this->formValuesArray["id"];
            
            //Check Edit Privileges
            $dac = new DataAccessComponentCurator;
            $curator = $dac->getCuratorByObjectId($memeId,null,null,null);
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
            foreach (explode(",",$this->formValuesArray["memeTaxonomies"]) as $taxonomy)
            {
                $taxonomyList[] = new Taxonomy(0,$taxonomy);
            }

            $purifier = HtmlValidation::getHtmlPurifier();
            $text = $purifier->purify($this->formValuesArray["text"]);
            $quote = $purifier->purify($this->formValuesArray["quote"]);

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
                    new Meme
                    (
                        $this->formValuesArray["id"]
                    	,$this->formValuesArray["memeTitle"]
                        ,CuratorSession::getCuratorFromSession()
                        ,($this->formValuesArray["disseminate"] == 'Y') ? 1 : 0
                        ,$datePublished                 //$datePublished
                        ,null                           //$date
                        ,$taxonomyList

                        ,$text
                        ,$quote
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
//ErrorCollection::addUserErrorMessage(0, "memeDeltaList:" . print_r($deltaList));

    	//SUCCESS/ERROR FLAG
        $success = '';

        // SEND OBJECTS TO BUSINESS LAYER IF NO ERRORS AND DELTAS EXIST
        if (ErrorCollection::hasUserErrorMessages() == false && $deltaExists)
        {
            try
            {
                $bc = new DataAccessComponentMeme();
                $memeId = $bc->saveMemeList($deltaList);
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

                ErrorCollection::addUserSuccessMessage(0, "Meme {$functionText}.");
                $success = "SUCCESS::";
            }
        }

        if
        (
            ErrorCollection::hasUserErrorMessages() == false
            &&
            (
                $function == Delta::INSERT
                ||
                (
                    isset($this->formValuesArray["delta"])
                    && $this->formValuesArray["delta"] == 'Y'
                )
            )
            &&
            (
                isset($this->formValuesArray["referenceid"])
                && $this->formValuesArray["referenceid"] != ''
            )
        )
        {
            try
            {
                $bc = new DataAccessComponentEntityRelationship();
                $bc->saveMemeReference($this->formValuesArray["referenceid"],$memeId);
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
            &&
            (
                $function == Delta::INSERT
                ||
                (
                    isset($this->formValuesArray["delta"])
                    && $this->formValuesArray["delta"] == 'Y'
                )
            )
            &&
            (
                isset($this->formValuesArray["schemaid"])
                && $this->formValuesArray["schemaid"] != ''
            )
        )
        {
            try
            {
                $bc = new DataAccessComponentEntityRelationship();
                $bc->saveSchemaMeme($this->formValuesArray["schemaid"],$memeId);
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
            ErrorCollection::addUserErrorMessage(0, "No modifications to Meme.");
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
            && get_class($this) != 'ProcessFormMemeModal')
        {
            $success = 'REDIRECT::';
            $url = ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS');
            if ($memeId && $function == Delta::INSERT)
            {
                $url .= "Meme/".$memeId."/";
            }
            elseif($function == Delta::DELETE)
            {
                $url .= "MemeList/domain=curator".$amp;
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
