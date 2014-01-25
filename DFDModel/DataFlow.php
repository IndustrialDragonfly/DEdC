<?php
require_once 'Link.php';
/**
 * Basic DFD element which links nodes together
 *
 * @author eugene
 */
class DataFlow extends Link 
{
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
      //if no parameters are passed just create a new instance
      if(func_num_args() == 0)
      {
         parent::__construct();
         $this->originNode = NULL;
         $this->destinationNode = NULL;
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
         $DataFlow_var = $pdo->query("SELECT * FROM dataflow WHERE id = '" . $this->getId() . "'")->fetch();
         if($DataFlow_var == FALSE)
         {
            throw new BadFunctionCallException("no matching id found in dataFlow DB");
         }
         $originNode_id = $DataFlow_var['origin_id'];
         $destinationNode_id = $DataFlow_var['dest_id'];
         
         if( $originNode_id != NULL)
         {
            $origin = $parent->getElementById($originNode_id);
            $this->setOriginNode($origin);
         }
         else
         {
            $this->originNode = NULL;
         }
         
         if( $destinationNode_id != NULL)
         {
            $destination = $parent->getElementById($destinationNode_id);
            $this->setDestinationNode($destination);
         }
         else
         {
            $this->destinationNode = NULL;
         }
      }
   }

   //</editor-fold>
   
   //<editor-fold desc="Accessor functions" defaultstate="collapsed">
   //<editor-fold desc="originNode functions" defaultstate="collapsed">
   /**
* function that returns the Node that this dataflow originates from
* @return Node
*/
   public function getOriginNode()
   {
      return $this->originNode;
   }
   
   /**
* function that sets the origin node to the specified node and adds itself to thats nodes list of links,
* if the origin node was already set it will first remove itself from that Nodes list of Links
* @param Node $aNode
* @throws BadFunctionCallException if the input was not a Node object
*/
   public function setOriginNode($aNode)
   {
      //make sure a Node object was passed
      if($aNode instanceof Node)
      {
         //if origin has not been set yet
         if ($this->originNode == NULL)
         {
            //set the origin node and add this DataFlow to its list of Links
            $this->originNode = $aNode;
            $aNode->addLink($this);
         }
         //if the origin node has already been set
         else
         {
            //remove this DataFlow from the old origin nodes list of links and
            //thenset the origin node to the new node and add this DataFlow to
            //its list of Links
            $this->originNode->removeLink($this);
            $this->originNode = $aNode;
            $aNode->addLink($this);
         }
      }
      else
      {
         throw new BadFunctionCallException("input parameter was not a Node");
      }
   }
   
   /**
* function that clears the origin node and removes it from the list of links of its old origin node
*/
   public function clearOriginNode()
   {
      if($this->originNode != NULL)
      {
         $this->originNode->removeLink($this);
         $this->originNode = NULL;
      }
   }
   //</editor-fold>
   
   //<editor-fold desc="destinationNode functions" defaultstate="collapsed">
   /**
* function that returns the Node that this dataflow ends at
* @return Node
*/
   public function getDestinationNode()
   {
      return $this->destinationNode;
   }
   
   /**
* function that sets the destination node to the specified node and adds itself to thats nodes list of links,
* if the destination node was already set it will first remove itself from that Nodes list of Links
* @param Node $aNode
* @throws BadFunctionCallException if the input was not a Node object
*/
   public function setDestinationNode($aNode)
   {
      //make sure a Node object was passed
      if($aNode instanceof Node)
      {
         //if destination has not been set yet
         if ($this->destinationNode == NULL)
         {
            //set the destination node and add this DataFlow to its list of Links
            $this->destinationNode = $aNode;
            $aNode->addLink($this);
         }
         //if the destination node has already been set
         else
         {
            //remove this DataFlow from the old destination nodes list of links and
            //then set the destination node to the new node and add this DataFlow to
            //its list of Links
            $this->destinationNode->removeLink($this);
            $this->destinationNode = $aNode;
            $aNode->addLink($this);
         }
      }
      else
      {
         throw new BadFunctionCallException("input parameter was not a Node");
      }
   }
   
   /**
* function that clears the origin node and removes it from the list of links of its old origin node
*/
   public function clearDestinationNode()
   {
      if($this->destinationNode != NULL)
      {
         $this->destinationNode->removeLink($this);
         $this->destinationNode = NULL;
      }
   }
   //</editor-fold>
   
   /**
* function that removes all of the connections to this DataFlow
*/
   public function removeAllLinks()
   {
      $this->clearOriginNode();
      $this->clearDestinationNode();
   }
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
      $type = get_class($this);
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
   
   /**
* load the Object's from the database
* @param PDO $pdo
*/
   public function load($pdo)
   {
      
   }
   //</editor-fold>
}
?>
