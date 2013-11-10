<?php
require_once 'Element.php';
require_once 'Constants.php';
/**
 * Description of DataFlow
 *
 * @author Josh Clark
 */
class DataFlow extends Element
{

   //<editor-fold desc="Attributes" defaultstate="collapsed">
   /**
    * this will be a Node object
    * @var Node
    */
   protected $originNode;
   /**
    * this will be a node object
    * @var Node
    */
   protected $destinationNode;
   //</editor-fold>
   
   //<editor-fold desc="Constructor" defaultstate="collapsed">
   public function __construct()
   {
      parent::__construct();
      $this->originNode = NULL;
      $this->destinationNode = NULL;
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
      $type = Constants::DataFlow;
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
      $insert_stmt = $pdo->prepare("INSERT INTO dataflow (id, origin_id, dest_id) VALUES(?,?,?)");
      // Bind the parameters of the prepared statement
      $insert_stmt->bindParam(1, $this->id);      
      $insert_stmt->bindParam(2, $this->originNode->getId());
      $insert_stmt->bindParam(3, $this->destinationNode->getId());
      // Execute, catch any errors resulting
      $insert_stmt->execute();
      //</editor-fold>
   }
   //</editor-fold>
}
?>
