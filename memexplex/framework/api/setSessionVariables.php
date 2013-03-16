<?php

/**
 * This file sets session variabls via an AJAX call.
 *
 * @package Framework
 * @subpackage API
 * @author Ryan Somma
 */

//LOAD __autoload() FUNCTION TO LOAD CLASSES
require_once 'autoloadClass.php';

//INITIALIZE SESSION VARIABLES
if (isset($_POST['application']))
{
    Constants::setConstant('CURRENT_APPLICATION', $_POST['application']);
    ApplicationSession::initialize(CURRENT_APPLICATION);
}
else
{
    throw new ControlExceptionMissingParameter('setSessionVariable: Missing application variable.');
}

if (isset($_POST['pageCode']))
{
    Constants::setConstant('CURRENT_PAGE_CODE', $_POST['pageCode']);
    PageSession::initialize(CURRENT_PAGE_CODE);
}
else
{
    throw new ControlExceptionMissingParameter('setSessionVariable: Missing page variable.');
}


// ARRAY OF $_GET VARIABLE/KEY PAIRS
$sessionVariablesArray = $_POST;

foreach ($sessionVariablesArray as $key => $value)
{
    if ($key != 'application' && $key != 'pageCode')
    {
        if (ApplicationSession::validateName($key))
        {
            ApplicationSession::setValue($key, $value);
        }
        else
        {
            PageSession::setValue($key, $value);
        }
    }
}

echo $key . '=' . $value;

/**
 * LOG ALL COLLECTED ERRORS TO APPROPRIATE LOGS
 */
ErrorCollection::logAllErrors();

