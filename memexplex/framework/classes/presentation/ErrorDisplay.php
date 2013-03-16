<?php

/**
 * Displays erros and success messages to the user.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma 08/21/2008
 * @see Html
 */
class ErrorDisplay extends Html
{

    /**
     * Set the error display source.
     */
    public function setSource()
    {

        //-------------------------
        //NEED: GATHER ERRORS FROM QUERY STRINGS
        //-------------------------

        if ('none' != PageConfiguration::getCurrentPageErrorDisplay())
        {

            $this->source =
"\n<!-- BEGIN DISPLAY ERROR -->\n"
."<span id=\"errorDisplaySpan\">";

            $this->buildErrorsHtmlDisplay();

            $this->source .=
"</span>"
."\n<!-- END DISPLAY ERROR -->\n";

            $this->source .= $this->externalSource;

            Benchmark::setBenchmark('ErrorDisplay.php', __FILE__, __LINE__);
        }
        else
        {
            $this->source = $this->externalSource;
        }
    }

    /**
     * Gets Error Html.
     *
     * @param bool $getCodes Whether to highlight fields in error.
     */
    public function buildErrorsHtmlDisplay($getCodes = false)
    {

        //-------------------------
        //NEED: GATHER ERRORS FROM QUERY STRINGS
        //-------------------------

        if (ApplicationSession::getValue('debugFlag'))
        {
            $this->buildApplicationErrorsHtmlDisplay();
        }

        $this->buildUserErrorMessagesHtmlDisplay($getCodes);

        $this->buildUserSuccessMessagesHtmlDisplay();

    }

    /**
     * Get application error messages for display.
     */
    public function buildApplicationErrorsHtmlDisplay()
    {
        //
        //GET APPLICATION ERROR MESSAGES
        //
        if ( ErrorCollection::hasErrors() )
        {
            $messageString = "";
            $messages = ErrorCollection::getErrorMessagesArray();

            for ($i=0;$i<count($messages);$i++)
            {
                $messageString .= $messages[$i]['errstr']."&nbsp;&nbsp;";
            }

            $messageString .= "<a href=\"#\" onclick=\"showhide('moreError');\">[more]</a>"
            . "<div id=\"moreError\" style=\"display: none;\"><p>"
            . ErrorCollection::getErrorMessages()
            . "</div>";

            $this->source .=
"<div>"
.    "<a id=\"aApplicationError\" class=\"NoLink\" style=\"visibility:visible\">"
.        "<span class=\"ErrorRed\">&nbsp;APPLICATION ERROR:&nbsp;</span></a>"
.        "<span class=\"ErrorRed\">&nbsp;&nbsp; {$messageString}</span>"
."</div>"
."<script type=\"text/javascript\" defer=\"defer\">"
.  "FlashText('aApplicationError','on',500,10);"
."</script>";

        }

    }

    /**
     * Builds user-generated error display.
     *
     * @param bool $getCodes Whether to highlight formfields in error.
     */
    public function buildUserErrorMessagesHtmlDisplay($getCodes = false)
    {
        //
        //GET USER ERROR MESSAGES
        //
        if (ErrorCollection::hasUserErrorMessages())
        {
            $messageString = "";
            $messageCodes = "";
            $messages = ErrorCollection::getUserErrorMessagesArray();

            for ($i=0;$i<count($messages);$i++)
            {
                //ELMINATE DUPLICATE ERROR MESSAGES
                if (false == strstr($messageString,$messages[$i]['errstr']))
                {
                    $messageString .= $messages[$i]['errstr']."&nbsp;&nbsp;";
                }
                //HIGHLIGHTS FORM FIELD THAT IS IN ERROR
                if (
                        $getCodes
                        && $messages[$i]['errno'] != ''
                   )
                {
                    $messageCodes .=
                        "highlightFormError('{$messages[$i]['errno']}');";
                }
            }

            $this->source .=
"<div>"
.    "<a id=\"aUserError\" class=\"NoLink\" style=\"visibility:visible\">"
.        "<span class=\"ErrorRed\">&nbsp;ERROR:&nbsp;</span></a>"
.        "<span class=\"ErrorRed\">&nbsp;&nbsp; {$messageString}</span>"
."</div>"
."<script type=\"text/javascript\" defer=\"defer\">"
.  "FlashText('aUserError','on',500,10);{$messageCodes}"
."</script>";

        }

    }

    /**
     * Triggers a URL redirection with messages.
     * @param string $url Redirect URL.
     */
    public function buildRedirect($url)
    {
        $messageString = "";
        $messages = ErrorCollection::getUserSuccessMessagesArray();

        for ($i=0;$i<count($messages);$i++)
        {
            $messageString .= $messages[$i]['errstr']."  ";
        }

        $this->source .=
'REDIRECT::'
.'<script type="text/javascript" defer="defer">'
.'window.location.assign("'.$url.'successMessage='.$messageString.'")'
.'</script>';
    }

    /**
     * Builds a green-colored user success message display.
     */
    public function buildUserSuccessMessagesHtmlDisplay()
    {
        //
        //GET USER ERROR MESSAGES
        //
        if (ErrorCollection::hasUserSuccessMessages() || isset($_GET['successMessage']))
        {
            $messageString = "";
            if (ErrorCollection::hasUserSuccessMessages())
            {
                $messages = ErrorCollection::getUserSuccessMessagesArray();

                for ($i=0;$i<count($messages);$i++)
                {
                    $messageString .= $messages[$i]['errstr']."&nbsp;&nbsp;";
                }
                $js = "FlashText('aUserSuccess','on',500,10);";
            }

            if (isset($_GET['successMessage']))
            {
                $messageString .= $_GET['successMessage'];
                $js = "setTimeout('FlashText(\'aUserSuccess\',\'on\',500,10)',200);";
            }

            $this->source .=
"<div>"
.    "<a id=\"aUserSuccess\" class=\"NoLink\" style=\"visibility:visible\">"
.        "<span class=\"ErrorGreen\">&nbsp;SUCCESS:&nbsp;</span></a>"
.        "<span class=\"ErrorGreen\">&nbsp;&nbsp; {$messageString}</span>"
."</div>"
."<script type=\"text/javascript\" defer=\"defer\">"
.  $js
."</script>";

        }

    }

    /**
     * Gives a "This Should Not Happen" erro to the user.
     * @param Exception $exception
     */
    public static function unexpectedExceptionDisplay($exception)
    {
//        $application = Constants::getConstant('CURRENT_APPLICATION');
//
//        $bc = new ApplicationAdministratorBusinessComponent();
//        $applicationAdministrators = $bc->getApplicationAdministrators($application);

//        return "An unexpected error has occured in processing your request."
//                . " Please contact <a href=\"mailto:"
//                . "D05-SMB-ALC_Cust_Support@uscg.mil;"
//                . "?subject=ALMIS Database error."
//                . "&cc="
//                . $applicationAdministrators->getEmailList()
//                . "\">ALC Customer Support</a>.";
        return "An unexpected error has occured in processing your request."
                . " Please contact <b><em>ryan at ideonexus.com</em></b>"
                . " and have him look into it.";
    }

}
