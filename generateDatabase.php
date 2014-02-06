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
$dfd->setLabel("New_DFD!");
$dfd->setOrganization("DEdC");
$dfd->setOriginator("The Eugene");
$dfd->save();

$process = new Process($storage, $dfd->getId());
$process->setLabel("Some Proc");
$process->setLocation(10, 50);
$process->setOrganization("DEdC");
$process->setOriginator("The Eugene");
$process->save();

$dataStore = new DataStore($storage, $dfd->getId());
$dataStore->setLabel("Some Store");
$dataStore->setLocation(30, 50);
$dataStore->setOrganization("DEdC");
$dataStore->setOriginator("The Eugene");
$dataStore->save();

$externalInteractor = new ExternalInteractor($storage, $dfd->getId());
$externalInteractor->setLabel("Some Interactor");
$externalInteractor->setLocation(20, 50);
$externalInteractor->setOrganization("DEdC");
$externalInteractor->setOriginator("The Eugene");
$externalInteractor->save();

$multiprocess = new Multiprocess($storage, $dfd->getId());
$multiprocess->setLabel("Some Multiprocess");
$multiprocess->setLocation(35, 10);
$multiprocess->setOrganization("DEdC");
$multiprocess->setOriginator("The Eugene");
$multiprocess->save();

$dataFlow = new DataFlow($storage, $dfd->getId());
$dataFlow->save();
$dataFlow->setOriginNode($process);
$dataFlow->setDestinationNode($multiprocess);
$dataFlow->setLabel("Some Dataflow");
$dataFlow->setLocation(15, 22);
$dataFlow->setOrganization("DEdC");
$dataFlow->setOriginator("The Eugene");
$dataFlow->update();