<?php

/**
 * Classloader for User Model
 * @param String $classname
 */
function UserModelClassLoader($classname)
{
// User Model
    if (file_exists("Models/UserModel/" . $classname . ".php"))
    {
        require_once "Models/UserModel/" . $classname . ".php";
    }
    elseif (file_exists("Models/UserModel/AuthenticationModules/" . $classname . ".php"))
    {
        require_once "Models/UserModel/AuthenticationModules/" . $classname . ".php";
    }
 }