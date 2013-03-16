<?php

/**
 * Handles values submitted from an HTML form. Form will appear HTML screen
 * where pagecode matches the name of this class ProcessFormPAGECODE
 *
 * @package MemexPlex
 * @subpackage Business
 * @see ProcessForm
 * @see ProcessFormInterface
 * @author Dr. Gediman
 */

class ProcessFormCuratorProfile extends ProcessForm
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
        $function = Delta::UPDATE;

        try
        {
            $email = ApplicationSession::getValue('CURATOR_EMAIL');
            
            if ($this->formValuesArray["displayname"] == "")
            {
                ErrorCollection::addUserErrorMessage("displayname", "Please provide a display name.");
            }
            
            try
            {
                $dac = new DataAccessComponentCurator();
                $curator = $dac->getCurator($email);
                
                if (isset($curator))
                {
                    $curator->setDisplayName($this->formValuesArray["displayname"]);
                    $curator->setPublishByDefault(($this->formValuesArray["disseminatebydefault"] == 'Y') ? 1 : 0);
                    
                    //ADD DELTA OBJECT TO DELTALIST
                    $delta = new Delta($curator,$function);
                }
                else
                {
                    ErrorCollection::addUserErrorMessage("email", "Account not found. Please log in to modify your profile.");
                }
            }
            catch (PersistenceException $e)
            {
                throw new BusinessException('Error loading Curator.');
            }
            
            $deltaExists = true;
        }
        catch (BusinessException $e)
        {
            ErrorCollection::addUserErrorMessage(0, $e);
        }

//DEBUGGING STATEMENTS FOR DEVELOPERS, UNCOMMENT LINES TO SEE ARRAYS OF VALUES BEING PROCESSED
//ErrorCollection::addUserErrorMessage(0, "formValuesArray:" . print_r($this->formValuesArray));
//ErrorCollection::addUserErrorMessage(0, "memeDeltaList:" . print_r($delta));

    	//SUCCESS/ERROR FLAG
        $success = '';
        // SEND OBJECTS TO BUSINESS LAYER IF NO ERRORS AND DELTAS EXIST
        if (ErrorCollection::hasUserErrorMessages() == false && $deltaExists)
        {
            try
            {
                $dac = new DataAccessComponentCurator();
                $dac->saveCurator($delta);
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
                //Serialize Curator into session.
                CuratorSession::setCuratorSession($curator);
                ErrorCollection::addUserSuccessMessage(0, "Profile updated.");
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
            ErrorCollection::addUserErrorMessage(0, "No modifications to Profile.");
        }

        //IF NOT SUCCESSFUL SET ERROR FLAG FOR PRESENTATION LAYER
        if (ErrorCollection::hasUserErrorMessages())
        {
            $success = "ERROR::";
        }

        // FORMAT MESSAGES FOR PRESENTATION
        $errors = new ErrorDisplay();
        if ($success == 'SUCCESS::'
            && isset($_COOKIE['CURATOR_ID']))
        {
            $success = 'REDIRECT::';
            $url = ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS');
            $url .= "CuratorProfile/setcookie=true&";
            $errors->buildRedirect($url);
        }
        else
        {
            $errors->buildErrorsHtmlDisplay(true);
        }
        
        return $success . $errors->getSource();
    }

}
