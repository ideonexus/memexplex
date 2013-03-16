<?php

/**
 * This is the file called via AJAX. It accepts a collection of POST variables
 * from a form submission and routes them to the appropriate processform class
 * for handling.
 *
 * @package Framework
 * @subpackage API
 * @author Ryan Somma
 */

    //DETERMINE THE ROOT FOLDER FOR ALL LINKS
    define
    (
        'ROOT_FOLDER'
        ,substr
        (
            $_SERVER['PHP_SELF']
            ,0
            ,strpos($_SERVER['PHP_SELF'], "framework/api/processForm.php")
        )
    );

    // LOAD __autoload() FUNCTION TO LOAD CLASSES
    require_once $_SERVER['DOCUMENT_ROOT'] . ROOT_FOLDER . 'framework/api/autoloadClass.php';

    // ARRAY OF $_GET VARIABLE/KEY PAIRS
    $formValuesArray = array();
    // LOOP THROUGH POST VARIABLES, DECODE THEM, AND STRIP ESCAPE SLASHES
    $tempArray = $_POST;
    foreach ($tempArray as $key=>$value)
    {
        $formValuesArray[$key] = stripslashes(urldecode($value));
    }

    //INITIALIZE SESSION VARIABLES
    Constants::setConstant('AJAX_METHOD', true);
    if (isset($formValuesArray['application']))
    {
        Constants::setConstant('CURRENT_APPLICATION', $formValuesArray['application']);
    }
    else
    {
        die('Missing application variable.');
    }
    ApplicationSession::initialize(CURRENT_APPLICATION);

    /**
     * INITIALIZE ERROR AND EXCEPTION HANDLING
     */
    ErrorCollection::initialize();

    /**
     * INITIALIZE BENCHMARKING
     */
    Benchmark::initialize();
    Benchmark::setBenchmark('Initial Items', __FILE__, __LINE__);

    /**
     * INITIALIZE ERROR AND EXCEPTION HANDLING
     */

    if (isset($formValuesArray['pageCode']))
    {
        Constants::setConstant('CURRENT_PAGE_CODE', $formValuesArray['pageCode']);
        PageConfiguration::getInstance($formValuesArray['pageCode']);
        /**
         * CHECK IF CURATOR SESSION HAS EXPIRED
         */
        CuratorSession::validateCuratorSession();
        PageConfiguration::getInstance($formValuesArray['pageCode']);
        $processFormObject = ProcessFormFactory::create($formValuesArray['pageCode']);
        $processFormObject->setValuesArray($formValuesArray);
        //Gotta have atleast this much to continue.
        if (PageConfiguration::getCurrentPageSecurity() == 'none'
            || CuratorSession::checkAddPrivileges())
        {
            $processFormContent = $processFormObject->process();
        }
        else
        {
            ErrorCollection::addUserErrorMessage(
                0
                ,"Insufficient permissions for this operation."
            );
            $errors = new ErrorDisplay();
            $errors->buildErrorsHtmlDisplay(true);
            $processFormContent = "ERROR::".$errors->getSource();
        }

        if (ApplicationSession::getValue('debugFlag'))
        {
            echo "<b>AJAX Call URL for Debugging:<p><pre>"
                 . print_r($formValuesArray)
                 . "</pre><b>";
        }
        echo $processFormContent;
    }
    else
    {
        ErrorCollection::addUserErrorMessage(0, 'Missing page code.');
        echo 'Application error: Missing page code.';
    }

    /**
     * LOG ALL COLLECTED ERRORS TO APPROPRIATE LOGS
     */
    ErrorCollection::logAllErrors();
