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
      $originNode = NULL;
      $destinationNode = NULL;
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
      return $originNode;
   }
   
   /**
    * function that sets the orgin node to the specified node and adds itself to thats nodes list of links,
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
         if ($orginNode = NULL)
         {
            //set the origin node and add this DataFlow to its list of Links
            $originNode = $aNode;
            $aNode->addLink($this);
         }
         //if the origin node has already been set
         else
         {
            //remove this DataFlow from the old origin nodes list of links and 
            //thenset the origin node to the new node and add this DataFlow to 
            //its list of Links
            $originNode->removeLink($this);
            $originNode = $aNode;
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
   public function clearOrginNode()
   {
      if($orginNode != NULL)
      {
         $originNode->removeLink($this);
         $orginNode = NULL;
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
      return $destinationNode;
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
         if ($destinationNode = NULL)
         {
            //set the destination node and add this DataFlow to its list of Links
            $destinationNode = $aNode;
            $aNode->addLink($this);
         }
         //if the destination node has already been set
         else
         {
            //remove this DataFlow from the old destination nodes list of links and 
            //then set the destination node to the new node and add this DataFlow to 
            //its list of Links
            $destinationNode->removeLink($this);
            $destinationNode = $aNode;
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
      if($destinationNode != NULL)
      {
         $destinationnNode->removeLink($this);
         $destinationNode = NULL;
      }
   }
   //</editor-fold>
   
   /**
    * function that removes all of the connections to this DataFlow
    */
   public function removeAllLinks()
   {
      clearOrginNode();
      clearDestinationNode();
   }
   //</editor-fold>
}
?>
