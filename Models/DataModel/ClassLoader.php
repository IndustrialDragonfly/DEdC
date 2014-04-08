<?php

/**
 * Classloader for DataModel
 * @param String $classname
 */
function DataModelClassLoader($classname)
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
    
    // Data (DFD) Model
    if (file_exists($prefix . "Models/DataModel/" . $classname . ".php"))
    {
        require_once $prefix . "Models/DataModel/" . $classname . ".php";
    }
}
