<?php

/**
 * Handles values submitted from an HTML form. Form will appear HTML screen
 * where pagecode matches the name of this class ProcessFormPAGECODE
 *
 * @package MemexPlex
 * @subpackage Business
 * @author Professor John Nerdelbaum Frink, Jr.
 */

class ProcessFormLogin extends ProcessForm
implements ProcessFormInterface
{

    /**
     * Validates the Curator against the database.
     *
     * @param Delta $delta
     * @return Delta $delta
     * @throws {@link BusinessException}
     */
    protected function validateCurator(Delta $delta)
    {
        $curatorDelta = $delta->getObject();
        try
        {
            $dac = new DataAccessComponentCurator();
            $curator = $dac->getCurator($curatorDelta->getEmail());

            //Is the email address in our database?
            //If not, suggest they sign up.
            if (!isset($curator))
            {
                ErrorCollection::addUserErrorMessage
                (
                    0
                    ,"Account not found. Would you like to "
                    ."<a href=\""
                    .ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
                    ."SignUp/\">Sign Up</a>?"
                );
                return false;
            }

            //If curator is unvalidated, check the $_GET id against
            //the id assigned them by the system as a sha1 hash key.
            if ($curator->getLevel()->getId() == '0'
                && $curatorDelta->getId() == sha1($curator->getId()))
            {
                //If passwords' sha1 hash keys match, start the
                //curator off in the system at 6th level.
                if (sha1($curator->getId().$curatorDelta->getPassword()) == $curator->getPassword())
                {
                    $curatorDelta->setId($curator->getId());
                    $curatorDelta->setDisplayName($curator->getDisplayName());
                    $curatorDelta->setPassword($curator->getPassword());
                    $curatorDelta->setLevel(new CuratorLevel(6));
                    $curatorDelta->setPublishByDefault($curator->getPublishByDefault());
                    $delta =
                        new Delta
                        (
                            $curatorDelta
                            ,Delta::UPDATE
                        );
                    $dac->saveCurator($delta);
                }
                else
                {
                    ErrorCollection::addUserErrorMessage(0, "Invalid password.");
                    return false;
                }
            }
            elseif ($curator->getLevel()->getId() == '0')
            {
                ErrorCollection::addUserErrorMessage(
                	0
                    , "This account is unverified, please access the site"
                    ." with the link sent to your email address."
                );
                return false;
            }
/**
* [TODO] This is lazy in a bad way, violates the DRY Principle. We checked passwords above.
*/
            elseif (sha1($curator->getId().$curatorDelta->getPassword()) != $curator->getPassword())
            {
                ErrorCollection::addUserErrorMessage(0, "Invalid password.");
                return false;
            }
        }
        catch (PersistenceException $e)
        {
            throw new BusinessException('Error loading Curator.');
        }

        return $curator;
    }

    /**
     * Accepts the values of the form, validates them, assembles the
     * appropriate business entities, and sends them along to the appropriate DAC.
     *
     * @return string Errors/Success messages for the UI.
     */
    public function process()
    {
        try
        {
            $uid = null;
            if (isset($this->formValuesArray["uid"]))
            {
                $uid = $this->formValuesArray["uid"];
            }
            if ($this->formValuesArray["password"] == '')
            {
                ErrorCollection::addUserErrorMessage("password", "Please enter a password.");
            }
            if (!EmailValidation::validEmail($this->formValuesArray["email"]))
            {
                ErrorCollection::addUserErrorMessage("email", "A valid email is required.");
            }
            //Package Curator object in a Delta object for DAC.
            $delta =
                new Delta
                (
                    new Curator
                    (
                        $uid
                    	,$this->formValuesArray["email"]
                        ,null
                        ,$this->formValuesArray["password"]
                        ,null
                        ,null
                    )
                    ,Delta::UPDATE
                );
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
                $curator = $this->validateCurator($delta);
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
                ErrorCollection::addUserSuccessMessage(
                    0
                    ,"Login successful. Welcome ".$curator->getDisplayName()."!"
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
            $setCookie = "";
            $amp = "&";
            if ($this->formValuesArray["cookie"] == 'Y')
            {
                //Cookie can't be set here because headers are already
                //sent and this is an ajax call, flag the cookie to be
                //set when the login page redirects.
                $setCookie = $amp.'setcookie=true';
            }
            $url .= "memelist/domain=curator".$setCookie.$amp;
            $errors->buildRedirect($url);
        }
        else
        {
            $errors->buildErrorsHtmlDisplay(true);
        }

        return $success . $errors->getSource();
    }

}
