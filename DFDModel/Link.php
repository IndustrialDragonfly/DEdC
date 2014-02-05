<?php
require_once 'Element.php';
require_once 'Constants.php';
/**
 * Abstract class from which dataflows and similar objects will inherit
 * from
 *
 * @author Josh Clark
 * @author Eugene Davis
 */
abstract class Link extends Element
{
//<editor-fold desc="Attributes" defaultstate="collapsed">
   /**
    * UUID of a node object
    * @var String
    */
   protected $originNode;
   /**
    * UUID of a node object
    * @var String
    */
   protected $destinationNode;
   
   /**
    * Storage object
    * @var Writeable AND/OR Readable 
    */
   protected $storage;
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
       parent::__construct();
       $this->storage = func_get_arg(0);

      // Find if the type of the second argument is DFD, if so, its a new DFD
      if (is_subclass_of($this->storage->getTypeFromUUID(func_get_arg(1)), "DataFlowDiagram"))
      {
        $this->parent = func_get_arg(1);
      }
      //if the type of the second argument is not a DFD, then load from DB
      else if (func_num_args() == 2)
      {
         $this->storage = func_get_arg(0);
         $this->id = func_get_arg(1);
         
         $vars = $this->storage->loadLink($this->id);
         
         // As in other classes, this could probably be turned into a for
         // each loop in the future for greater flexibility
         
         // Perform mapping
         $this->label = $vars['label'];
         $this->originator = $vars['originator'];
         $this->x = $vars['x'];
         $this->y = $vars['y'];
         $this->originNode = $vars['origin_id'];
         $this->destinationNode = $vars['dest_id'];
         $this->parent = $vars['dfd_id'];
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
            $this->originNode = $aNode->getId();
            $aNode->addLink($this);
            $aNode->update();
         }
         //if the origin node has already been set
         else
         {
            //remove this DataFlow from the old origin nodes list of links and 
            //thenset the origin node to the new node and add this DataFlow to 
            //its list of Links
            $type = $this->storage->getTypeFromUUID($this->originNode);
            $node = new $type($this->storage, $this->originNode);
            $node->removeLink($this);
            $node->update();
            
            $this->originNode = $aNode->getId();
            $aNode->addLink($this);
            $aNode->update();
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
         $type = $this->storage->getTypeFromUUID($this->originNode);
         $node = new $type($this->storage, $this->originNode);
         $node->removeLink($this);
         $this->originNode = NULL;
         $node->update();
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
            $this->destinationNode = $aNode->getId();
            $aNode->addLink($this);
            $aNode->update();
         }
         //if the destination node has already been set
         else
         {
            //remove this DataFlow from the old destination nodes list of links and 
            //then set the destination node to the new node and add this DataFlow to 
            //its list of Links
            $type = $this->storage->getTypeFromUUID($this->destinationNode);
            $node = new $type($this->storage, $this->destinationNode);
            $node->removeLink($this);
            $node->update();
            
            $this->destinationNode = $aNode->getId();
            $aNode->addLink($this);
            $node->update();
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
         $type = $this->storage->getTypeFromUUID($this->destinationNode);
         $node = new $type($this->storage, $this->destinationNode);
         $node->removeLink($this);
         $node->update();
         $this->destinationNode = NULL;
      }
   }
   //</editor-fold>
   
   /**
    * Gets the parent DFD UUID and returns it to the caller.
    * @returns String
    */
   public function getParent()
   {
       $this->parent;
   }
   
   /**
    * Removes the specified node
    * Follows a Link-centric breaking approach - that is the link removes
    * itself from the node, rather than the node removing itself from the
    * link
    * 
    * @param Node $node
    */
   public function removeNode($node)
   {
       if ($node->getId() == $this->getOriginNode())
       {
           $this->clearOriginNode();
           // Actually call back the node that just called and remove the node
           // since Links ALWAYS break the connection off
           $node->removeLink($this);
           $node->update();
       }
       elseif ($node->getId() == $this->getDestinationNode())
       {
           $this->clearDestinationNode();
           $node->removeLink($this);
           $node->update();
       }
       else
       {
           // Throw exception
       }
   }
   
   /**
    * function that removes all of the connections to this DataFlow
    */
   public function removeAllNodes()
   {
      $this->clearOriginNode();
      $this->clearDestinationNode();
   }
   
   /**
    * Returns an assocative array representing the link object. This 
    * assocative array has the following elements and types:
    * id String
    * label String
    * originator String
    * organization String 
    * x Int
    * y Int
    * parent String
    * originNode String
    * destinationNode String
    * 
    * @return Mixed[]
    */
   public function getAssociativeArray()
   {
       // Get Entity and Element array
       $linkArray = parent::getAssocativeArray();
       
       // Add Link Attributes to array
       $linkArray['originNode'] = $this->originNode;
       $linkArray['destinationNode'] = $this->destinationNode;
       
       return $linkArray;
   }
   //</editor-fold>
   
//<editor-fold desc="Data Store Actions" defaultstate="collapsed">
   /**
* function that will save this object to the database
* @param WriteStorable $datastore this is the data store to write to
*/
   public function save()
   {
       // Send info required to save dataflow to the data store
       $this->storage->saveLink($this->id, $this->label, get_class($this), 
               $this->originator, $this->x, $this->y, $this->originNode, 
               $this->destinationNode, $this->parent);
   }
   
   /**
    * Deletes the link object from the data store
    * 
    * @param Writable $datastore
    */
   public function delete()
   {
       $this->removeAllNodes();
       $this->storage->deleteLink($this->id);
   }
   
   /**
    * Updates the link object in the data store
    * 
    * @param Writable $datastore
    */
   public function update()
   {
       // Temporary cheaty way, should see if a more effictient way is
       // available
       $this->delete();
       $this->save();
   }
   //</editor-fold>
}
?>
