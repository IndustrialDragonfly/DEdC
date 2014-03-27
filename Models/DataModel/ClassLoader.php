<?php

/**
 * Classloader for DataModel
 * @param String $classname
 */
function DataModelClassLoader($classname)
{
    // Data (DFD) Model
    if (file_exists("Models/DataModel/" . $classname . ".php"))
    {
        require_once "Models/DataModel/" . $classname . ".php";
    }
}
