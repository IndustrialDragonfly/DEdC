<?php

/**
 * ClassLoader for shared objects of both models
 * @param String $classname
 */
function ModelsClassLoader($classname)
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
    
    // Models
    if (file_exists($prefix . "Models/" . $classname . ".php"))
    {
        require_once $prefix . "Models/" . $classname . ".php";
    }   
}

