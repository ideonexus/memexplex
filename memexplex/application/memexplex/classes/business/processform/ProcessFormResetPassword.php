<?php

/**
 * Handles values submitted from an HTML form. Form will appear HTML screen
 * where pagecode matches the name of this class ProcessFormPAGECODE
 *
 * @package MemexPlex
 * @subpackage Business
 * @author Buddy Pine
 */

class ProcessFormResetPassword extends ProcessForm
implements ProcessFormInterface
{

    /**
     * Gets the curator and sends and email link to reset the password.
     *
     * @return string Errors/Success messages for the UI.
     */
    public function process()
    {
        try
        {
            if (!EmailValidation::validEmail($this->formValuesArray["email"]))
            {
                ErrorCollection::addUserErrorMessage("email", "A valid email is required.");
            }
        }
        catch (BusinessException $e)
        {
            ErrorCollection::addUserErrorMessage(0, $e);
        }

//DEBUGGING STATEMENTS FOR DEVELOPERS, UNCOMMENT LINES TO SEE ARRAYS OF VALUES BEING PROCESSED
//ErrorCollection::addUserErrorMessage(0, "formValuesArray:" . print_r($this->formValuesArray));
//ErrorCollection::addUserErrorMessage(0, "delta:" . print_r($delta));

    	//SUCCESS/ERROR FLAG
        $success = '';

        // SEND OBJECTS TO BUSINESS LAYER IF NO ERRORS
        if (ErrorCollection::hasUserErrorMessages() == false)
        {
            try
            {
                $dac = new DataAccessComponentCurator;
                $curator = $dac->getCurator($this->formValuesArray["email"]);
            }
            //IF UNEXPECTED ERRORS, SEND TO HANDLER TO
            //PROPERLY FORMAT FOR THE PRESENTATION LAYER
            catch (GeneralException $e)
            {
                ErrorCollection::addUserErrorMessage(
                    0
                    ,ErrorDisplay::unexpectedExceptionDisplay($e)
                );
            }

            if (!$curator)
            {
                ErrorCollection::addUserErrorMessage(
                    0,
                    "No account was found with that email."
                );
            }
            
            //IF SUCCESSFUL, SET SUCCESS FLAG FOR PRESENTATION LAYER
            if (false == ErrorCollection::hasUserErrorMessages())
            {
                //SEND A CONFIRMATION EMAIL
                $to = $this->formValuesArray["email"];
                $subject = "MemexPlex Password Reset";
                $link = ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                ."ChangePassword/"
                ."uid=".sha1($curator->getId());
                $body = <<< emailbody
Dear subscriber,

This email is in response to a request to
reset your password at MemexPlex.com. You
may click the link below to change your 
password, or you may disregard this email
if you made no such request:

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
                        "An email has been sent to you with instructions for reseting your password."
                    );
                    $success = "SUCCESS::";
                }
            }
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
