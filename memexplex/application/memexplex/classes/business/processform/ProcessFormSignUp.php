<?php

/**
 * Handles values submitted from an HTML form. Form will appear HTML screen
 * where pagecode matches the name of this class ProcessFormPAGECODE
 *
 * @package MemexPlex
 * @subpackage Business
 * @see ProcessForm
 * @see ProcessFormInterface
 * @author Doctor Zachary Smith
 */

class ProcessFormSignUp extends ProcessForm
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
        $function = Delta::INSERT;

        try
        {
            if ($this->formValuesArray["password"] == '')
            {
                ErrorCollection::addUserErrorMessage("password", "Please enter a password.");
                ErrorCollection::addUserErrorMessage("confirmpassword", "Please enter a password.");
            }
            elseif ($this->formValuesArray["password"]
                != $this->formValuesArray["confirmpassword"])
            {
                ErrorCollection::addUserErrorMessage("password", "Passwords must match.");
                ErrorCollection::addUserErrorMessage("confirmpassword", "Passwords must match.");
            }
            
            if (!EmailValidation::validEmail($this->formValuesArray["email"]))
            {
                ErrorCollection::addUserErrorMessage("email", "A valid email is required.");
            }

            if ($this->formValuesArray["displayname"] == "")
            {
                ErrorCollection::addUserErrorMessage("displayname", "Please provide a display name.");
            }
            
            //GET A RANDOMLY ASSIGNED ID FROM THE DATABASE
            $curatorid = DataAccessComponentCurator::getNewCuratorId();
            
            //ADD DELTA OBJECT TO DELTALIST
            $delta =
                new Delta
                (
                    new Curator
                    (
                        $curatorid
                    	,$this->formValuesArray["email"]
                        ,$this->formValuesArray["displayname"]
                        ,sha1($curatorid.$this->formValuesArray["password"])
                        ,1
                        ,new CuratorLevel(0)
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
            catch (PersistenceExceptionDuplicateEntry $e)
            {
                ErrorCollection::addUserErrorMessage
                (
                    0
                    ,"This email is already registered."
                );
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
                //SEND A CONFIRMATION EMAIL
                $to = $this->formValuesArray["email"];
                $subject = "MemexPlex Sign Up Confirmation";
                $link = ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                ."Login/"
                ."uid=".sha1($curatorid);
                $body = <<< emailbody
Dear subscriber,

This is an email confirmation that you have
signed up for an account with MemexPlex.com.
Please click on the link below to confirm your
enrollment. If you have not enrolled with
MemexPlex.com, please disregard this email.

$link

If clicking on the above link does not work,
please cut and paste it into your web browser's
address bar.

Thank you,

The MemexPlex support staff

emailbody;
                $headers = "From: ideo@memexplex.com";
                $emailsuccess = mail($to,$subject,$body,$headers);
                if (!$emailsuccess)
                {
                    ErrorCollection::addUserErrorMessage(
                        0,
                        "Email failed."
                    );
                }
                else
                {
                    ErrorCollection::addUserSuccessMessage(
                        0,
                        "Curator registered. Check your email for a confirmation link."
                    );
                    $success = "SUCCESS::";
                }
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
        $errors->buildErrorsHtmlDisplay(true);

        return $success . $errors->getSource();
    }

}
