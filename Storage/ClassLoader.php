<?php

/**
 * Classloader for Storage classes
 * @param String $classname
 */
function StorageClassLoader($classname)
{
    if (file_exists("Storage/" . $classname . ".php"))
    {
        require_once "Storage/" . $classname . ".php";
    }
}
