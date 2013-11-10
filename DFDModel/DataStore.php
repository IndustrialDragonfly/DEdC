<?php
require_once 'Node.php';
require_once 'Constants.php';
/**
 * Description of DataStore
 *
 * @author Josh Clark
 */
class DataStore extends Node
{

   //<editor-fold desc="Attributes" defaultstate="collapsed">
   //</editor-fold>
   
   //<editor-fold desc="Constructor" defaultstate="collapsed">
   public function __construct()
   {
      parent::__construct();
   }

   //</editor-fold>
   
   //<editor-fold desc="Accessor functions" defaultstate="collapsed">
   //</editor-fold>
   
   //<editor-fold desc=""DB functions defaultstate="collapsed">
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
      $type = Constants::DataStore;
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
      for ($i = 0; $i < $this->getNumberOfLinks(); $i++)
      {
         $insert_stmt = $pdo->prepare("INSERT INTO element (id, df_id) VALUES(?,?)");
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
