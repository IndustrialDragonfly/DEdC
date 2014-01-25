<?php
require_once 'Element.php';
/**
 * Node is the abstract class that governs all node objects, like process, datastore
 * etc. For all storage access methods, they could currently go in the Element
 * class, but this would reduce flexibility should the approach to loading
 * a DFD be changed (for instance so that Node objects could be loaded
 * independently of the DFD)
 *
 * @author Josh Clark
 */
 abstract class Node extends Element
{
   //<editor-fold desc="Attributes" defaultstate="collapsed">
   protected $links;
   //</editor-fold>
   
   //<editor-fold desc="Constructor" defaultstate="collapsed">
   /**
    * constructor. if no arguments are specified a new object is created with
    * a random id. if three arguments are specified, the oject is loaded from the
    * DB if an entry with a matching id exists
    * @param ReadStorable $datastore
    * @param string $id
    * @param DataFlowDiagram $parent
    */
   public function __construct()
   {
      parent::__construct();
      $this->links = array();
 
      //if 3 parameters are passed load the object with values from the DB
      if (func_num_args() == 3)
      {
         $datastore = func_get_arg(0);
         $this->id = func_get_arg(1);
         $parent = func_get_arg(2);
         
         $this->setParent($parent);
         
         $vars = $datastore->loadNode($this->id);
         
         // Potentially this section could be rewritten using a foreach loop
         // on the array and reflection on the current node to determine
         // what it should store locally
         $this->label = $vars['label'];
         $this->originator = $vars['originator'];
         $this->x = $vars['x'];
         $this->y = $vars['y'];
      }
   }

   //</editor-fold>
   
   //<editor-fold desc="Accessor functions" defaultstate="collapsed">
   
   /**
    * function that gets the number of links that connect to this node
    * @return type the number of links 
    */
   public function getNumberOfLinks()
   {
      return count($this->links);
   }
   
   /**
    * Function that return the links that connect to a node
    * @return string[]
    */
   public function getLinks()
   {
       return $links;
   }
   
   /**
    * function that adds a new link to the list of links
    * @param DataFlow $newLink
    * @throws BadFunctionCallException
    */
   public function addLink($newLink)
   {
      if($newLink instanceof DataFlow)
      {
         array_push($this->links, $newLink);
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
      if($link instanceof DataFlow)
      {
         //find if the link is in the list and get its location if it is
         $loc = array_search($link, $this->links, true);
         if ($loc !== false)
         {
            
            //remove the link from the list
            unset($this->links[$loc]);
            //normalize the indexes of the list
            $this->links = array_values($this->links);
            
            //code to find if this Node is the DataFlows orgin or destination
            if($this->isOrigin($link) == true)
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
   
   /**
    * Function that check to see if this Node is the origin or the destination 
    * of the specified dataflow, it will throw an exception if this node was 
    * not associated with that dataflow
    * @throws BadFunctionCallException
    */
   protected function isOrigin($link)
   {
      if($link instanceof DataFlow)
      {
         if ($this == $link->getOriginNode())
         {
            return TRUE;
         }
         elseif ($this == $link->getDestinationNode())
         {
            return FALSE;
         }
         else
         {
            throw new BadFunctionCallException("This DataFlow is not connected to this Node");
         }
      }
      else 
      {
         throw new BadFunctionCallException("input parameter was not a DataFlow");
      }
   }
   
   /**
    * Returns a specified link based upon where it is in the list
    * @param type $index integer
    * @return type DataFlow
    * @throws BadFunctionCallException if the value was out of bounds
    */
   public function getLinkbyPosition($index)
   {
      if ($index <= count($this->links) -1 && $index >= 0)
      {
         return $this->links[$index];
      }
      else
      {
         throw new BadFunctionCallException("input parameter was out of bounds");
      }
   }
   
   /**
    * a function that searches the list of DataFlows and returns one with a matching id
    * @param type $linkId
    * @return a DataFlow that has an id matching the specified one, will return null if not specified
    */
   public function getLinkbyId($linkId)
   {
      for ($i = 0; $i < count($this->links); $i++)
      {
         if($this->links[$i]->getId() == $linkId)
         {
            return $this->links[$i];
         }
      }
      return null;
   }
   
   /**
    * function that removes every link to this node
    */
   public function removeAllLinks()
   {
      while(count($this->links) != 0)
      {
         $this->removeLink($this->links[0]);
      }
   }
   //</editor-fold>
   //<editor-fold desc="save" defaultstate="collapsed">
    /**
    * function that will save this object to the data store
    */
    public function save($dataStore)
    {
        $dataStore->saveNode($this->id, $this->label, get_class($this), $this->originator, $this->x, $this->y, $this->links, $this->getNumberOfLinks());
    }

    //</editor-fold>
}
?>
