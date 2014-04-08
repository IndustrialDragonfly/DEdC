<?php

/**
 * Autoload function which loads a file based on class name from any of the 
 * relevant folders - those of the data models, those of Request and Response
 * folders, and those in the Storage folder
 * Must be last autoloader in register stack, so that errors don't interfer
 * with other autoloaders.
 * @param String $classname
 */
function GeneralLoader($classname)
{
    // Handle setting the path if there is one set - such as for PHPUnit
    if (isset($GLOBALS['path']))
    {
        $prefix = $GLOBALS['path'] . "/"; 
    }
    else
    {
        $prefix = "";
    }
    
    if (file_exists($prefix . $classname . ".php") )
    {
        require_once $prefix . $classname . ".php";
    }
}