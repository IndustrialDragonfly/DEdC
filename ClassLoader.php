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
    if (file_exists($classname . ".php") )
    {
        require_once $classname . ".php";
    }
    
    else
    {
        echo "Problem loading class " . $classname . " definition does not appear to exist.";
        exit;
    }
}