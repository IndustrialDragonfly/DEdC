<?php

/**
 * Attempts to load Interface classes (Request and Response).
 * @param String $classname
 */
function InterfaceClassLoader($classname)
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
    
    // Request
    if (file_exists($prefix . "Interface/Request/" . $classname . ".php"))
    {
        require_once $prefix . "Interface/Request/" . $classname . ".php";
    }
    elseif (file_exists($prefix . "Interface/Request/AuthenticationHandlers/" . $classname . ".php"))
    {
        require_once $prefix . "Interface/Request/AuthenticationHandlers/" . $classname . ".php";
    }
    
    // Response
    elseif (file_exists($prefix . "Interface/Response/" . $classname . ".php"))
    {
        require_once $prefix . "Interface/Response/" . $classname . ".php";
    }
}

