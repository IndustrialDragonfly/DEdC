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
      // Case when the constructor is passed only a storage object
      // and a parent DFD
      if (func_num_args() == 2)
      {
         // Since we don't require linking up to a DFD on construction,
         // the construction is almost identical to a node object'
         parent::__construct(func_get_arg(0), func_get_arg(1));
         $this->subDataFlowDiagram = NULL;
      }
      // If constructor is passed a storage object, parent DFD, and an ID,
      // load from storage
      else if (func_num_args() == 3)
      {
         parent::__construct(func_get_arg(0), func_get_arg(1), func_get_arg(2));
         
         // Load mapping of subDFDNode to DFD, unlike most load functions
         // this one returns a single value rather than an assocative array
         $this->subDataFlowDiagram = $this->storage->loadSubDFDNode($this->id);         
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
   
   /**
    * Returns an assocative array representing the entity object. This 
    * assocative array has the following elements and types:
    * id String
    * label String
    * originator String
    * organization String 
    * type String
    * genericType String
    * x Int
    * y Int
    * parent String
    * links String[]
    * subDataFlowDiagram String
    * 
    * @return Mixed
    */
   public function getAssociativeArray()
   {
       $subDFDNodeArray = parent::getAssociativeArray();
       $subDFDNodeArray['subDataFlowDiagram'] = $this->subDataFlowDiagram;
       
       return $subDFDNodeArray;
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
   //<editor-fold desc="Save/Delete/Update" defaultstate="collapsed">
   /**
* function that will save this object to the database
*/
   public function save()
   {
       // Call the Node object save function to do most of the work
       parent::save();
      // Call storage object's saveSubDFDNode
       $this->storage->saveSubDFDNode($this->subDataFlowDiagram, $this->id);
   }

   /**
    * Function that deletes this object from the database
    */
   public function delete()
   {
       // Call the parent delete function AFTER child delete function
       $this->storage->deleteSubDFDNode($this->id);
       
       parent::delete();
   }
   
   /**
    * Refreshes the object in the storage medium, probably should later have a 
    * dedicated function in the storage medium in the future.
    */
   public function update()
   {
       $this->delete();
       $this->save();
   }
   //</editor-fold>
}
?>

