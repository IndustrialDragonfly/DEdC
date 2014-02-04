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
 * @author Eugene Davis
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
      $this->storage = func_get_arg(0);
      // If only one argument, parent is NULL
      if (func_num_args() == 1)
      {
          $this->parent = NULL;
      }
      // Find if the type of the second argument is DFD, if so, its a new DFD
      elseif (is_subclass_of($this->storage->getTypeFromUUID(func_get_arg(1)), "DataFlowDiagram"))
      {
        $this->parent = func_get_arg(1);
      }
      //if the type of the second argument is not a DFD, then load from DB
      else
      {
         $this->id = func_get_arg(1);
         
         $vars = $this->storage->loadNode($this->id);
         
         // Potentially this section could be rewritten using a foreach loop
         // on the array and reflection on the current node to determine
         // what it should store locally
         $this->label = $vars['label'];
         $this->originator = $vars['originator'];
         $this->x = $vars['x'];
         $this->y = $vars['y'];
         $this->links = $vars['links'];
         $this->parent = $vars['dfd_id'];
      }
   }

   //</editor-fold>
   
   //<editor-fold desc="Accessor functions" defaultstate="collapsed">
   
   /**
    * function that gets the number of links that connect to this node
    * @return int the number of links 
    */
   public function getNumberOfLinks()
   {
      return count($this->links);
   }
   
   /**
    * Function that return the links that connect to a node
    * @return string[] an array of all the uuid of the links to this node
    */
   public function getLinks()
   {
       return $this->links;
   }
   
   /**
    * Function that adds a new link to the list of links
    * Should ONLY be called by a object descended from Link, otherwise may
    * break DFD
    * 
    * @param DataFlow $newLink
    * @throws BadFunctionCallException
    */
   public function addLink($newLink)
   {
       // Check that it is a link, and that it isn't already in the array
       // This allows either link or node to add link, without going into
       // infinite look
      if($newLink instanceof Link)
      {
          if (!array_search($newLink->getId(), $this->links))
          {
            array_push($this->links, $newLink->getId());
          }
      }
      else
      {
         throw new BadFunctionCallException("input parameter was not a Link");
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
         if($this->links[$i] == $linkId)
         {
            return $this->links[$i];
         }
      }
      return null;
   }
   //</editor-fold>
    
    /**
    * removes a specified DataFlow from the list of links
    * Should only be called by Link object
    * @param type $link the link to be removed
    * @return boolean if the link was in the array
    * @throws BadFunctionCallException if the input was not a DataFlow
    */   
   public function removeLink($link)
   {
      if(is_subclass_of($link, "Link"))
      {
         //find if the link is in the list and get its location if it is
         $loc = array_search($link->getId(), $this->links);
         if ($loc !== FALSE)
         {
            
            //remove the link from the list
            unset($this->links[$loc]);
            //normalize the indexes of the list
            $this->links = array_values($this->links);
            return true;
         }
         else
         {
            throw new BadFunctionCallException("Input parameter not contained in Node");
         }
      }
      else
      {
         throw new BadFunctionCallException("Input parameter was not descended from Link");
      }
   }
   
    /**
    * function that removes every link to this node, used when deleting a node
    */
   public function removeAllLinks()
   {
       // Counts down to avoid any ambigutity with unsetting of things in
       // links
       for ($i = count($this->links); $i > 0; $i--)
       {
           $type = $this->storage->getTypeFromUUID($this->links[0]);
           $link = new $type($this->storage, $this->links[0]);
           $link->removeNode($this);
           $link->update();
           // The call to link will actually call removeLink in this node
           // cleaning itself out - because Link descendents objects are the ONLY 
           // objects that can call removeLink
       }
       // Previous code probably needs to get a result from link
       // before calling this part of code - since this is meant
       // for in memory usauge only, as the database should already
       // have been updated to reflected
       unset($this->links);
       $this->links = array();
   }
   
   //<editor-fold desc="Save/Delete/Update" defaultstate="collapsed">
    /**
    * function that will save this object to the data store
     * 
     * @param WriteStorable $dataStore
    */
    public function save()
    {
        $this->storage->saveNode($this->id, $this->label, get_class($this), 
                $this->originator, $this->x, $this->y, $this->links, 
                $this->getNumberOfLinks());
    }

    /**
     * Deletes the node, including cleaning up the links that are connected
     * to it
     * @param WriteStorable $dataStore
     */
    public function delete()
    {
        // Remove all links
        $this->removeAllLinks();
        
        // Delete node itself
        $this->storage->deleteNode($this->id);
    }
    
    /**
     * Updates the node with new information. For now, it cheats by just deleting
     * then recreating the node
     * 
     * @param WriteStorable and ReadStorable $dataStore
     */
    public function update()
    {
        $this->storage->deleteNode($this->id);
        $this->save();
    }
    //</editor-fold>
}
?>
