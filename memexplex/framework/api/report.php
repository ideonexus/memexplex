<?php

/**
 * This file grabs HTML content for a page to be displayed via an AJAX call.
 *
 * @package Framework
 * @subpackage API
 * @author Ryan Somma
 */
    ini_set('session.gc_maxlifetime', '86400');
    session_start();
    
    //DETERMINE THE ROOT FOLDER FOR ALL LINKS
    define
    (
        'ROOT_FOLDER'
        ,substr
        (
            $_SERVER['PHP_SELF']
            ,0
            ,strpos($_SERVER['PHP_SELF'], "framework/api/report.php")
        )
    );

    //LOAD __autoload() FUNCTION TO LOAD CLASSES
    require_once 'autoloadClass.php';

    // ARRAY OF $_GET VARIABLE/KEY PAIRS
    $filterArray = $_POST;

    $reportContent = null;

    //INITIALIZE SESSION VARIABLES
    Constants::setConstant('AJAX_METHOD', true);
    if (isset($filterArray['application']))
    {
        Constants::setConstant('CURRENT_APPLICATION', $filterArray['application']);
    }
    else
    {
        die('Missing application variable.');
    }
    ApplicationSession::initialize(CURRENT_APPLICATION);
    Constants::setConstant('CURRENT_PAGE_CODE', $filterArray['pageCode']);
    PageSession::initialize(CURRENT_PAGE_CODE);

		/**
		* Set the width and height for the submodal windows.
		*/
		Constants::setConstant('SUBMODAL_CLASS', 'submodal-650-500');

    /**
     * INITIALIZE ERROR AND EXCEPTION HANDLING
     */
    ErrorCollection::initialize();

    /**
     * INITIALIZE BENCHMARKING
     */
    Benchmark::initialize();
    Benchmark::setBenchmark('Initial Items', __FILE__, __LINE__);

    PageConfiguration::getInstance(CURRENT_PAGE_CODE);

    /**
     * CHECK IF CURATOR SESSION HAS EXPIRED
     */
    CuratorSession::validateCuratorSession();

    $reportObject = HtmlContentFactory::create($filterArray['pageCode']);
    $reportObject->setFilterArray($filterArray);
    $reportObject->setSource();
    $reportContent = $reportObject->getSource();

    //$reportContent = $reportClassname . "\n" . $orgId;
    if (ApplicationSession::getValue('debugFlag'))
    {
        echo '<b>AJAX Call URL for Debugging:<p>' . Url::selfURL() . '<b>';
    }

    echo $reportContent;

    /**
     * LOG ALL COLLECTED ERRORS TO APPROPRIATE LOGS
     */
    ErrorCollection::logAllErrors();
