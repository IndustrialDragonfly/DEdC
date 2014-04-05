<?php
/**
 * ClassLoader for AuthenticationInformation classes
 * @param String $classname
 */
function AuthenicationInformationClassLoader($classname)
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
    
    if (file_exists($prefix . "AuthenticationInformation/" . $classname . ".php"))
    {
        require_once $prefix . "AuthenticationInformation/" . $classname . ".php";
    }
 }

