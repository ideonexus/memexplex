<?php

/**
 * The functions herein negate the need to explicitly include
 * files for class definitions; instead, these functions will
 * map the classes directories and load the CLASSNAME.php
 * file when the CLASSNAME is instantiated.
 *
 * @author Ryan Somma, Craig Avondo
 */

/**
 * Maps the framework and applications classes directories and
 * loads them into session.
 *
 * [TODO Autoload class directories should be moved into cache.]
 *
 * @param string $application The application directory where
 * 		additional class files reside.
 */
function initializeAutoLoadClassDirectories($application = null)
{
    if (!isset($_SESSION['classDirectories']))
    {
        //GET FRAMEWORK CLASSES
        $_SESSION['classDirectories'] = array();
        $dir = new RecursiveDirectoryIterator
        (
            $_SERVER['DOCUMENT_ROOT'] . ROOT_FOLDER . 'framework/classes/'
        );
        foreach (new RecursiveIteratorIterator($dir, 2) as $path)
        {
            if (!$path->isDir())
            {
                $classFile = $path->getFilename();
                $_SESSION['classDirectories'][$classFile] =
                    str_replace('\\', '/', $path);
            }
        }

        if (isset($application))
        {
            //GET APPLICATION-SPECIFIC DIRECTORIES
            $appdir = new RecursiveDirectoryIterator
            (
                $_SERVER['DOCUMENT_ROOT'] . ROOT_FOLDER . 'application/' . $application . '/classes/'
            );
            foreach (new RecursiveIteratorIterator($appdir, 2) as $apppath)
            {
                if (!$apppath->isDir())
                {
                    $classFile = $apppath->getFilename();
                    $_SESSION['classDirectories'][$classFile] =
                        str_replace('\\', '/', $apppath);
                }
            }
        }

    }
}

/**
 * This function will automagically require_once instantiated classes.
 *
 * @param string $className The name of the class to auto load.
 * @link http://us3.php.net/autoload (08/21/2008)
 */
function autoloadClass($className)
{
    if (!isset($_SESSION['classDirectories']))
    {
        initializeAutoLoadClassDirectories('memexplex');
    }
    $classFile = $className . '.php';
    if (!isset($_SESSION['classDirectories'][$classFile]))
    {
        throw new ControlExceptionClassNotFound
        (
            'The requested library, "' . $classFile . '", could not be found.'
        );
    }
    $classPath = $_SESSION['classDirectories'][$classFile];
    require_once $classPath;
}

/**
 * register our autoload function with PHP's __autoload stack
 */
spl_autoload_register('autoloadClass');

