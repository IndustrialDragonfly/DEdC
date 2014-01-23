<?php
require_once 'Link.php';
/**
 * Basic DFD element which links nodes together
 *
 * @author eugene
 */
class DataFlow extends Link 
{
    public function __construct()
    {
        parent::__construct();
    }
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
      $type = Types::DataFlow;
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
      
      //<editor-fold desc="save to DataFlow table" defaultstate="collapsed">
      // Prepare the statement
      if($this->originNode != NULL && $this->destinationNode != NULL)
      {
         $insert_stmt = $pdo->prepare("INSERT INTO dataflow (id, origin_id, dest_id) VALUES(?,?,?)");
         // Bind the parameters of the prepared statement
         $insert_stmt->bindParam(1, $this->id);      
         $insert_stmt->bindParam(2, $this->originNode->getId());
         $insert_stmt->bindParam(3, $this->destinationNode->getId());
         // Execute, catch any errors resulting
         $insert_stmt->execute();
      }
      else if($this->originNode == NULL && $this->destinationNode != NULL)
      {
         $insert_stmt = $pdo->prepare("INSERT INTO dataflow (id, dest_id) VALUES(?,?)");
         // Bind the parameters of the prepared statement
         $insert_stmt->bindParam(1, $this->id);
         $insert_stmt->bindParam(2, $this->destinationNode->getId());
         // Execute, catch any errors resulting
         $insert_stmt->execute();
      }
      else if($this->originNode != NULL && $this->destinationNode == NULL)
      {
         $insert_stmt = $pdo->prepare("INSERT INTO dataflow (id, origin_id) VALUES(?,?)");
         // Bind the parameters of the prepared statement
         $insert_stmt->bindParam(1, $this->id);      
         $insert_stmt->bindParam(2, $this->originNode->getId());
         // Execute, catch any errors resulting
         $insert_stmt->execute();
      }
      if($this->originNode == NULL && $this->destinationNode == NULL)
      {
         $insert_stmt = $pdo->prepare("INSERT INTO dataflow (id) VALUES(?)");
         // Bind the parameters of the prepared statement
         $insert_stmt->bindParam(1, $this->id);
         // Execute, catch any errors resulting
         $insert_stmt->execute();
      }
      //</editor-fold>
   }
   //</editor-fold>
}
