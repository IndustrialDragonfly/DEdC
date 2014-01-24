<?php

require_once 'Node.php';
require_once 'Constants.php';

/**
 * Description of ExternalInteractor
 *
 * @author Josh Clark
 */
class ExternalInteractor extends Node
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
      if (func_num_args() == 0)
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

         $Entity_var = $pdo->query("SELECT * FROM entity WHERE id = '" . $this->getId() . "'")->fetch();


         if ($Entity_var == FALSE)
         {
            throw new BadFunctionCallException("no matching id found in entity DB");
         }
         $this->id = $Entity_var['id'];
         $this->label = $Entity_var['label'];
         $this->originator = $Entity_var['originator'];
         $Element_var = $pdo->query("SELECT * FROM element WHERE id = '" . $this->getId() . "'")->fetch();
         if ($Element_var == FALSE)
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
* function that will save this object to the database
* @param PDO $pdo this is the connection to the Database
*/
    public function save($pdo)
    {
        //<editor-fold desc="save to Entity table" defaultstate="collapsed">
        // Prepare the statement
        $insert_stmt = $pdo->prepare("INSERT INTO entity (id, label, type, originator) VALUES(?,?,?,?)");

        // Bind the parameters of the prepared statement
        $type = Types::ExternalInteractor;
        $insert_stmt->bindParam(1, $this->id);
        $insert_stmt->bindParam(2, $this->label);
        $insert_stmt->bindParam(3, $type);
        $insert_stmt->bindParam(4, $this->originator);

        // Execute, catch any errors resulting
        $insert_stmt->execute();
        //</editor-fold>
        //<editor-fold desc="save to Element table" defaultstate="collapsed">
        // Prepare the statement
        $insert_stmt = $pdo->prepare("INSERT INTO element (id, x, y) VALUES(?,?,?)");

        // Bind the parameters of the prepared statement
        $insert_stmt->bindParam(1, $this->id);
        $insert_stmt->bindParam(2, $this->x);
        $insert_stmt->bindParam(3, $this->y);

        // Execute, catch any errors resulting
        $insert_stmt->execute();
        //</editor-fold>
        //<editor-fold desc="save to Node table" defaultstate="collapsed">
        // Prepare the statement
        $insert_stmt = $pdo->prepare("INSERT INTO node (id, df_id) VALUES(?,?)");
        for ($i = 0; $i < $this->getNumberOfLinks(); $i++)
        {
            // Bind the parameters of the prepared statement
            $insert_stmt->bindParam(1, $this->id);
            $insert_stmt->bindParam(2, $this->links[$i]->getId());
            // Execute, catch any errors resulting
            $insert_stmt->execute();
        }
        //</editor-fold>
    }

    //</editor-fold>
}

?>