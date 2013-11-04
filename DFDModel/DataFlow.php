<?php
include_once 'Element.php';
/**
 * Description of DataFlow
 *
 * @author Josh Clark
 */
class DataFlow extends Element
{

   //<editor-fold desc="Attributes" defaultstate="collapsed">
   protected $originNode;
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
}
?>
