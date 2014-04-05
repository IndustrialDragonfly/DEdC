<?php

/**
 * Classloader for Storage classes
 * @param String $classname
 */
function StorageClassLoader($classname)
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
    
    if (file_exists($prefix . "Storage/" . $classname . ".php"))
     {
        require_once $prefix . "Storage/" . $classname . ".php";
     }
}
