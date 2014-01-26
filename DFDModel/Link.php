<?php
require_once 'Element.php';
require_once 'Constants.php';
/**
 * Abstract class from which dataflows and similar objects will inherit
 * from
 *
 * @author Josh Clark
 */
abstract class Link extends Element
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
/**
* constructor. if no arguments are specified a new object is created with
* a random id. if three arguments are specified, the oject is loaded from the
* DB if an entry with a matching id exists
* @param ReadStorable $storage
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
         $storage = func_get_arg(0);
         $this->id = func_get_arg(1);
         $parent = func_get_arg(2);
         
         $this->setParent($parent);
         
         $vars = $storage->loadLink($this->id);
         
         // As in other classes, this could probably be turned into a for
         // each loop in the future for greater flexibility
         
         // Perform mapping
         $this->label = $vars['label'];
         $this->originator = $vars['originator'];
         $this->x = $vars['x'];
         $this->y = $vars['y'];
         $originNode_id = $vars['origin_id'];
         $destinationNode_id = $vars['dest_id'];
         
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
   
      //<editor-fold desc="Save" defaultstate="collapsed">
   /**
* function that will save this object to the database
* @param WriteStorable $datastore this is the data store to write to
*/
   public function save($datastore)
   {
       // Check if origin node is connected
       $origin_id = NULL;
       if ($this->originNode != NULL)
       {
           $origin_id = $this->originNode->getId();
       }
       
       // Check if destination node is connected
       $dest_id = NULL;
       if ($this->destinationNode != NULL)
       {
           $dest_id = $this->destinationNode->getId();
       }
       
       // Send info required to save dataflow to the data store
       $datastore->saveLink($this->id, $this->label, get_class($this), 
               $this->originator, $this->x, $this->y, $origin_id, 
               $dest_id);
   }
   
   //</editor-fold>
}
?>
