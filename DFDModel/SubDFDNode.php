<?php

require_once 'Node.php';
require_once 'DataFlowDiagram.php';

/**
 * Description of SubDFDNode
 *
 * @author Josh Clark
 */
class SubDFDNode extends Node
{

   //<editor-fold desc="Attributes" defaultstate="collapsed">
   protected $subDataFlowDiagram;

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
         $this->subDataFlowDiagram = new DataFlowDiagram();
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
         

         //retrieve the data for the element part of the object
         $mySQLstatement = $pdo->prepare("SELECT * FROM element WHERE id=?");
         $mySQLstatement->bindParam(1, $this->getId());
         $mySQLstatement->execute();
         $Element_var = $mySQLstatement->fetch();
         if($Element_var == FALSE)
         {
            throw new BadFunctionCallException("no matching id found in element DB");
         }
         $this->x = $Element_var['x'];
         $this->y = $Element_var['y'];
         
         //skip loading the list of links as they should be generated when you load the dataflows
         
         //load the sub DFD
         //get the id of the DFD
         $mySQLstatement = $pdo->prepare("SELECT * FROM multiprocess WHERE mp_id=?");
         $mySQLstatement->bindParam(1, $this->getId());
         $mySQLstatement->execute();
         $multiprocess_var = $mySQLstatement->fetch();
         //pass the DB handler, the id of the dfd to load, and this multiprocess object
         $this->subDataFlowDiagram = new DataFlowDiagram($pdo, $multiprocess_var['dfd_id'], $this);
         
      }
   }

   //</editor-fold>
   //<editor-fold desc="Accessor functions" defaultstate="collapsed">
   /**
*
* @return DataFlowDiagram
*/
   public function getSubDFD()
   {
      return $this->subDataFlowDiagram;
   }

   /**
*
* @param DataFlowDiagram $aDiagram a new DFD to set the sub DFD to
* @throws BadFunctionCallException if the input is not a DFD
*/
   public function setSubDFD($aDiagram)
   {
      if ($aDiagram instanceof DataFlowDiagram)
      {
         $this->subDataFlowDiagram = $aDiagram;
      }
      else
      {
         throw new BadFunctionCallException("input parameter was not a DataFlowDiagram");
      }
   }

   //</editor-fold>
   //<editor-fold desc="overriding functions" defaultstate="collapsed">
   /**
* function that adds a new link to the list of links
* @param DataFlow $newLink
* @throws BadFunctionCallException
*/
   public function addLink($newLink)
   {
      if ($newLink instanceof DataFlow)
      {
         array_push($this->links, $newLink);
         $this->subDataFlowDiagram->addExternalLink($newLink);
      }
      else
      {
         throw new BadFunctionCallException("input parameter was not a DataFlow");
      }
   }

   /**
* removes a specified DataFlow from the list of links
* @param type $link the link to be removed
* @return boolean if the link was in the array
* @throws BadFunctionCallException if the input was not a DataFlow
*/
   public function removeLink($link)
   {
      if ($link instanceof DataFlow)
      {
         //find if the link is in the list and get its location if it is
         $loc = array_search($link, $this->links, true);
         if ($loc !== false)
         {
            //remove the link from the list
            unset($this->links[$loc]);
            //normalize the indexes of the list
            $this->links = array_values($this->links);
            $this->subDataFlowDiagram->removeExternalLink($link);
            //code to find if this Node is the DataFlows orgin or destination
            if ($this->isOrigin($link) == true)
            {
               //clear the origin of the link
               $link->clearOriginNode();
            }
            else
            {
               // clear the destination of the link
               $link->clearDestinationNode();
            }
            return true;
         }
         else
         {
            return false;
         }
      }
      else
      {
         throw new BadFunctionCallException("input parameter was not a DataFlow");
      }
   }

   //</editor-fold>
   //<editor-fold desc="DB functions" defaultstate="collapsed">
   /**
* function that will save this object to the database
* @param PDO $pdo this is the connection to the Database
*/
   public function save($pdo)
   {
      //save the sub DFD
      $this->subDataFlowDiagram->save($pdo);
      //<editor-fold desc="save to Entity table" defaultstate="collapsed">
      // Prepare the statement
      $insert_stmt = $pdo->prepare("INSERT INTO entity (id, label, type, originator) VALUES(?,?,?,?)");

      // Bind the parameters of the prepared statement
      $type = Types::Multiprocess;
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
      //<editor-fold desc="save to multiprocess table" defaultstate="collapsed">
      // Prepare the statement
      //$this->subDataFlowDiagram->save($pdo);
      $insert_stmt = $pdo->prepare("INSERT INTO multiprocess (dfd_id, mp_id) VALUES(?,?)");

      // Bind the parameters of the prepared statement
      $insert_stmt->bindParam(1, $this->subDataFlowDiagram->getId());
      $insert_stmt->bindParam(2, $this->id);

      // Execute, catch any errors resulting
      $insert_stmt->execute();
      //</editor-fold>
   }

   //</editor-fold>
}
?>

