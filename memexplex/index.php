<?php

/**
 * This is the Front-Controller. All access to the
 * application must go through this doohickey, which
 * will load the class libraries, validate the user,
 * figure out what page is being accessed, etc, etc.
 * It's all very modern and cutting edge, we assure
 * you.
 *
 * @author Ryan Somma
 */
    //DETERMINE THE ROOT FOLDER FOR ALL LINKS
    define
    (
        'APPLICATION_DOMAIN'
        ,"." . str_replace("www.","",$_SERVER['HTTP_HOST'])
    );
    ini_set('session.cookie_domain', APPLICATION_DOMAIN);
    
    /**
     * These are niceties to uncomment when developing in a
     * local environment: strict errors, unsessionalized class directories...
     * Turning these options on makes us write cleaner code.
    */
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    /**
     * Sessions expire in 24 hours of non-use to support all-nighters.
     */
    ini_set('session.gc_maxlifetime', '86400');
    session_start();
//    error_reporting(E_ALL | E_STRICT);
    
    //DETERMINE THE ROOT FOLDER FOR ALL LINKS
    define
    (
        'ROOT_FOLDER'
        ,substr
        (
            $_SERVER['PHP_SELF']
            ,0
            ,strpos($_SERVER['PHP_SELF'], basename(__FILE__))
        )
    );

    if (isset($_SESSION['classDirectories']))
    {
        unset($_SESSION['classDirectories']); //exit;
    }

    /**
     * LOAD __autoload() FUNCTION TO LOAD CLASSES
     * This allows PHP to automagically access the appropriate PHP files
     * when a class is instantiated rather than having to reference them
     * with includes.
     */
    require_once $_SERVER['DOCUMENT_ROOT'] . ROOT_FOLDER . 'framework/api/autoloadClass.php';

    /**
     * Load Application and Framework class directories
     * for the AutoLoad function. The 'memexplex' parameter
     * references the /application/memexplex/ directory.
     */
    initializeAutoLoadClassDirectories('memexplex');

    /**
     * Set the CURRENT_APPLICATION to memexplex. If the framework
     * is to support multiple applications, this will have to
     * change to a more dynamic means of being set based on URL.
     */
    Constants::setConstant('CURRENT_APPLICATION', 'memexplex');

    /**
     * The AJAX_METHOD constant is used throughout the application
     * to determine whether the page is being accessed through an
     * AJAX call. If so, it will turn off headers, skip reloading items
     * from the database that are already on the page, etc.
     */
    if (
    	isset($_GET['ajaxMethod'])
    	&& $_GET['ajaxMethod'] == 'true'
    )
    {
    	Constants::setConstant('AJAX_METHOD', true);
    }
    else
    {
    	Constants::setConstant('AJAX_METHOD', false);
    }

    /**
     * INITIALIZE APPLICATION SESSION VARIABLES
     * The ApplicationSession class is a workaround for PHP's lack of
     * namespaces. This will look silly and archaic post PHP 5.3.0.
     */
    ApplicationSession::initialize(Constants::getConstant('CURRENT_APPLICATION'));

    /**
     * CHECK IF CURATOR SESSION HAS EXPIRED
     * AND VALIDATE CURATOR
     */
    CuratorSession::validateCuratorSession();

    /**
     * VARIABLE TO MAKE ALL CLIENT-SIDE LINKS
     * SERVER-RELATIVE TO SUPPORT URL REWWRITING
     */
    if (!ApplicationSession::isNameSet('CURRENT_APPLICATION_DIRECTORY'))
    {
        ApplicationSession::setValue
        (
            'CURRENT_APPLICATION_DIRECTORY'
            ,substr
            (
                $_SERVER['PHP_SELF']
                ,0
                ,strpos($_SERVER['PHP_SELF'], "index.php")
            )
        );
    }

    /**
     * INITIALIZE ENVIRONMENT VARIABLES
     */
    new Environment();

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
     * INITIALIZE PAGECONFIGURATION FOR PAGE-SPECIFIC PROPERTIES
     */
    if (isset($_GET['pageCode']))
    {
        Constants::setConstant('CURRENT_PAGE_CODE'
        		, PageConfiguration::verifyPageCode($_GET['pageCode'])
        );
        PageConfiguration::getInstance(CURRENT_PAGE_CODE);
    }
    //If no pageCode, default to MemeList page.
    else
    {
        Constants::setConstant('CURRENT_PAGE_CODE', 'MemeList');
        PageConfiguration::getInstance('MemeList');
    }

    /**
     * INITIALIZE PAGE SESSION VARIABLES
     * Similar to ApplicationSession, this provides a namespace
     * for session variables at the page level.
     */
    PageSession::initialize(CURRENT_PAGE_CODE);

    Benchmark::setBenchmark('PageConfiguration', __FILE__, __LINE__);

    /**
     * Initialize the page and set its HTML source.
     */
    $page = new Page;
    $page->setSource();

    Benchmark::setBenchmark('Page.php', __FILE__, __LINE__);

    /**
     * Display the page HTML
     */
    echo $page->getSource();

    Benchmark::setBenchmark('Page Totals', __FILE__, __LINE__);

    /**
     * Benchmarking logToFile commented out until
     * we determine if it is actually needed.
     */
    //Benchmark::mark('after');
    //$benchmarkDetails = '';
    //$benchmarkDetails .= "Benchmark Details--------------------\n";
    //$benchmarkDetails .= "  Memory Usage: ".Benchmark::memory_usage()."\n";
    //$benchmarkDetails .= "  Memory Peak Usage: ".Benchmark::memory_peak_usage()."\n";
    //$benchmarkDetails .= "  Total Elapsed Time: ".Benchmark::elapsed_time('code_start', 'after')."\n\n";
    //Log::logToFile('benchmark', $benchmarkDetails);

    /**
     * LOG ALL COLLECTED ERRORS TO APPROPRIATE LOGS
     */
    ErrorCollection::logAllErrors();

    /**
     * GET AND DISPLAY ERRORS AT PAGE FOOTER IF DEBUG IS ON
     */
    if (ApplicationSession::getValue('debugFlag'))
    {
        echo ErrorCollection::getAllMessages();
    }
