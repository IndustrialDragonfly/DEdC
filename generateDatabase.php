<?php

/**
 * Somewhat poorly written way to generate a database full of things from 
 * data model so that we have something to test early integration on
 */

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
    elseif (file_exists("DFDModel/" . $classname . ".php"))
    {
        require_once "DFDModel/" . $classname . ".php";
    }
    elseif (file_exists("Storage/" . $classname . ".php"))
    {
        require_once "Storage/" . $classname . ".php";
    }
    else
    {
        // Make throw an exception later
        echo "Problem loading file";
        exit;
    }    
}

$storage = new DatabaseStorage();

$dfd = new DataFlowDiagram($storage);
$dfd->save();

$process = new Process($storage, $dfd->getId());
$process->save();

$dataStore = new DataStore($storage, $dfd->getId());
$dataStore->save();

$externalInteractor = new ExternalInteractor($storage, $dfd->getId());
$externalInteractor->save();

$multiprocess = new Multiprocess($storage, $dfd->getId());
$multiprocess->save();

$dataFlow = new DataFlow($storage, $dfd->getId());
$dataFlow->setOriginNode($process);
$dataFlow->setDestinationNode($dataStore);
$dataFlow->save();

