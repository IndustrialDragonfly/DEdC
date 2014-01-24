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
* @param PDO $pdo
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
         $pdo = func_get_arg(0);
         $this->id = func_get_arg(1);
         $parent = func_get_arg(2);
         
         $this->setParent($parent);
         
         //$Entity_var = $pdo->query("SELECT * FROM entity WHERE id = '" . $this->getId() . "'")->fetch();
         $mySQLstatement = $pdo->prepare("SELECT * FROM entity WHERE id=?");
         $mySQLstatement->bindParam(1, $this->getId());
         $mySQLstatement->execute();
         $Entity_var = $mySQLstatement->fetch();
         if($Entity_var == FALSE )
         {
            throw new BadFunctionCallException("no matching id found in entity DB");
         }
         $this->id = $Entity_var['id'];
         $this->label = $Entity_var['label'];
         $this->originator = $Entity_var['originator'];
         
         $Element_var = $pdo->query("SELECT * FROM element WHERE id = '" . $this->getId() . "'")->fetch();
         if($Element_var == FALSE)
         {
            throw new BadFunctionCallException("no matching id found in element DB");
         }
         $this->x = $Element_var['x'];
         $this->y = $Element_var['y'];
         
         
      }
    }

    //</editor-fold>
    //<editor-fold desc="Accessor functions" defaultstate="collapsed">
    //</editor-fold>
    //<editor-fold desc="DB functions" defaultstate="collapsed">
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


