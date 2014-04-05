<?php

/**
 * Classloader for User Model
 * @param String $classname
 */
function UserModelClassLoader($classname)
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
    
    // User Model
    if (file_exists($prefix . "Models/UserModel/" . $classname . ".php"))
    {
        require_once $prefix . "Models/UserModel/" . $classname . ".php";
    }
    elseif (file_exists($prefix . "Models/UserModel/AuthenticationModules/" . $classname . ".php"))
    {
        require_once $prefix . "Models/UserModel/AuthenticationModules/" . $classname . ".php";
    }
 }