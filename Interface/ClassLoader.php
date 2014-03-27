<?php

/**
 * Attempts to load Interface classes (Request and Response).
 * @param String $classname
 */
function InterfaceClassLoader($classname)
{
    // Request
    if (file_exists("Interface/Request/" . $classname . ".php"))
    {
        require_once "Interface/Request/" . $classname . ".php";
    }
    elseif (file_exists("Interface/Request/AuthenticationHandlers/" . $classname . ".php"))
    {
        require_once "Interface/Request/AuthenticationHandlers/" . $classname . ".php";
    }
    
    // Response
    elseif (file_exists("Interface/Response/" . $classname . ".php"))
    {
        require_once "Interface/Response/" . $classname . ".php";
    }
}

