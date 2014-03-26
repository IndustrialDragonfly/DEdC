<?php

/**
 * ClassLoader for shared objects of both models
 * @param String $classname
 */
function ModelsClassLoader($classname)
{
    // Models
    if (file_exists("Models/" . $classname . ".php"))
    {
        require_once "Models/" . $classname . ".php";
    }   
}

