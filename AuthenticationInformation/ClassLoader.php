<?php
/**
 * ClassLoader for AuthenticationInformation classes
 * @param String $classname
 */
function AuthenicationInformationClassLoader($classname)
{
    if (file_exists("AuthenticationInformation/" . $classname . ".php"))
    {
        require_once "AuthenticationInformation/" . $classname . ".php";
    }
 }

