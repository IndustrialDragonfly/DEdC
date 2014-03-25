<?php

/**
 * Autoload function which loads a file based on class name from any of the 
 * relevant folders - those of the data models, those of Request and Response
 * folders, and those in the Storage folder
 * @param String $classname
 */
function __autoload($classname)
{
    if (file_exists($classname . ".php") )
    {
        require_once $classname . ".php";
    }
    
    // Request
    elseif (file_exists("Interface/Request/" . $classname . ".php"))
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
    
    // DFD Model
    elseif (file_exists("Models/DataModel/" . $classname . ".php"))
    {
        require_once "Models/DataModel/" . $classname . ".php";
    }
    
    // Storage
    elseif (file_exists("Storage/" . $classname . ".php"))
    {
        require_once "Storage/" . $classname . ".php";
    }
    
    // User Model
    elseif (file_exists("Models/UserModel/" . $classname . ".php"))
    {
        require_once "Models/UserModel/" . $classname . ".php";
    }
    elseif (file_exists("Models/UserModel/AuthenticationModules/" . $classname . ".php"))
    {
        require_once "Models/UserModel/AuthenticationModules/" . $classname . ".php";
    }
    
    // Auth Info
    elseif (file_exists("AuthenticationInformation/" . $classname . ".php"))
    {
        require_once "AuthenticationInformation/" . $classname . ".php";
    }
    
    // Models
    elseif (file_exists("Models/" . $classname . ".php"))
    {
        require_once "Models/" . $classname . ".php";
    }
    
    else
    {
        echo "Problem loading class " . $classname . " definition does not appear to exist.";
        exit;
    }
}