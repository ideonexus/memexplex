<?php

/**
 * Handles values submitted from an HTML form. Form will appear HTML screen
 * where pagecode matches the name of this class ProcessFormPAGECODE
 *
 * @package MemexPlex
 * @subpackage Business
 * @see ProcessForm
 * @see ProcessFormInterface
 * @author Dr. Horrible
 */

class ProcessFormChangePassword extends ProcessForm
implements ProcessFormInterface
{

    /**
     * Validates the Curator key against the database.
     *
     * @param Delta $delta
     * @return boolean
     * @throws {@link BusinessException}
     */
    protected function validateKey(Delta $delta)
    {
        $curatorDelta = $delta->getObject();
        try
        {
            $dac = new DataAccessComponentCurator();
            $curator = $dac->getCurator($curatorDelta->getEmail());

            //If curator is unvalidated, check the $_GET id against
            //the id assigned them by the system as a sha1 hash key.
            if ($curatorDelta->getId() == sha1($curator->getId()))
            {
                return true;
            }

        }
        catch (PersistenceException $e)
        {
            throw new BusinessException('Error loading Curator.');
        }

        return false;
    }

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
            if (!ApplicationSession::isNameSet('CURATOR_ID')
                && !EmailValidation::validEmail($this->formValuesArray["email"])
            )
            {
                ErrorCollection::addUserErrorMessage("email", "A valid email is required.");
            }
            elseif (!ApplicationSession::isNameSet('CURATOR_ID'))
            {
                $email = $this->formValuesArray["email"];
            }
            elseif (ApplicationSession::isNameSet('CURATOR_ID'))
            {
                $email = ApplicationSession::getValue('CURATOR_EMAIL');
            }
            
            if (isset($this->formValuesArray["uid"]))
            {
                $uid = $this->formValuesArray["uid"];
            }
            elseif (!ApplicationSession::isNameSet('CURATOR_ID'))
            {
                ErrorCollection::addUserErrorMessage(
                    0
                    ,"Missing reset password key. Please try again using the email sent to you."
                );
            }
            
            if ($this->formValuesArray["password"] == '')
            {
                ErrorCollection::addUserErrorMessage("password", "Please enter a password.");
                ErrorCollection::addUserErrorMessage("confirmpassword", "Please enter a password.");
            }
            elseif ($this->formValuesArray["password"]
                != $this->formValuesArray["confirmpassword"]
            )
            {
                ErrorCollection::addUserErrorMessage("password", "Passwords must match.");
                ErrorCollection::addUserErrorMessage("confirmpassword", "Passwords must match.");
            }

            try
            {
                $dac = new DataAccessComponentCurator();
                $curator = $dac->getCurator($email);
                
                if (isset($curator))
                {
                    //If curator is not logged in, check the $_GET id against
                    //the id assigned them by the system as a sha1 hash key.
                    if (!ApplicationSession::isNameSet('CURATOR_ID')
                        && $uid != sha1($curator->getId())
                    )
                    {
                        ErrorCollection::addUserErrorMessage(
                            0
                            , "Missing reset password key. Please try again using the email sent to you."
                        );
                    }
                    $curator->setPassword(sha1($curator->getId().$this->formValuesArray["password"]));
                    
                    //ADD DELTA OBJECT TO DELTALIST
                    $delta = new Delta($curator,$function);
                }
                else
                {
                    ErrorCollection::addUserErrorMessage("email", "Account not found. Please use the email you used when you signed up.");
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
            
            if (ErrorCollection::hasUserErrorMessages() == false && $deltaExists)
            {
                //Serialize Curator into session.
                CuratorSession::setCuratorSession($curator);
                ErrorCollection::addUserSuccessMessage(
                    0,
                    "Password updated."
                );
                $success = "SUCCESS::";
            }
        }

        //IF NOT SUCCESSFUL SET ERROR FLAG FOR PRESENTATION LAYER
        if (ErrorCollection::hasUserErrorMessages())
        {
            $success = "ERROR::";
        }

        // FORMAT MESSAGES FOR PRESENTATION
        $errors = new ErrorDisplay();
        if ($success == 'SUCCESS::')
        {
            $success = 'REDIRECT::';
            $url = ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS');
            $url .= "MemeList/domain=curator&";
            $errors->buildRedirect($url);
        }
        else
        {
            $errors->buildErrorsHtmlDisplay(true);
        }
        
        return $success . $errors->getSource();
    }

}
