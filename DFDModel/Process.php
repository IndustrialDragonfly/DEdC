<?php

require_once 'Node.php';
require_once 'Constants.php';
require_once 'Storage/DatabaseStorage.php';

/**
 * Description of Process
 *
 * @author Josh Clark
 */
class Process extends Node
{


    //<editor-fold desc="Attributes" defaultstate="collapsed">
    //</editor-fold>
    //<editor-fold desc="Constructor" defaultstate="collapsed">
   /**
* constructor. if no arguments are specified a new object is created with
* a random id. if three arguments are specified, the oject is loaded from the
* DB if an entry with a matching id exists
* @param ReadStorable $datastore
* @param string $id
* @param DataFlowDiagram $parent
*/
   public function __construct()
    {
      if(func_num_args() == 0)
      {
         parent::__construct();
      }
      //if 3 parameters are passed load the object with values from the DB
      else if (func_num_args() == 3)
      {
         parent::__construct();
         $datastore = func_get_arg(0);
         $this->id = func_get_arg(1);
         $parent = func_get_arg(2);
         
         $this->setParent($parent);
         
         $vars = $datastore->loadNode($this->id);
         
         // Potentially this section could be rewritten using a foreach loop
         // on the array and reflection on the current node to determine
         // what it should store locally
         $this->label = $vars['label'];
         $this->originator = $vars['originator'];
         $this->x = $vars['x'];
         $this->y = $vars['y'];
      }
    }

    //</editor-fold>
    //<editor-fold desc="Accessor functions" defaultstate="collapsed">
    //</editor-fold>
    //<editor-fold desc="save" defaultstate="collapsed">
    /**
    * function that will save this object to the data store
    */
    public function save($dataStore)
    {
        $dataStore->saveNode($this->id, $this->label, get_class(), $this->originator, $this->x, $this->y, $this->links, $this->getNumberOfLinks());
    }

    //</editor-fold>
}

?>


