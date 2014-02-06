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
      // If constructor is passed a storage object, and an ID,
      // load from storage
      else if (func_num_args() == 2)
      {
         parent::__construct(func_get_arg(0), func_get_arg(1));
         
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
       $subDFDNodeArray = parent::getAssocativeArray();
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
       parent::addLink($newLink);
       // Check if this is equal to null - if it is, this can't happen yet
       // This function as such must be called when a new DFD is linked up to
       // this subDFDNode
       if ($this->subDataFlowDiagram !== NULL)
       {
            if (is_subclass_of($newLink, "Link"))
            {
                $this->subDataFlowDiagram->addExternalLink($newLink);
            }
            else
            {
               throw new BadFunctionCallException("Input parameter was not a Link");
            }
       }
   }

/**
* Removes a specified DataFlow from the list of links
* @param type $link the link to be removed
* @return boolean if the link was in the array
* @throws BadFunctionCallException if the input was not a DataFlow
*/
   public function removeLink($link)
   {
       // If removed the link from the Node object and subDFD exists, remove from
       // the subDFD
       if (parent::removeLinks() && $this->subDataFlowDiagram != NULL)
       {
           $this->subDataFlowDiagram->removeExternalLink($link);
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
       $this->storage->saveSubDFDNode($this->parent, $this->id);
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
       // Cannot call removeAllLinks in Node delete function
       $this->storage->deleteSubDFDNode($this->id);
       $this->storage->deleteNode($this->id);
       $this->save();
   }
   //</editor-fold>
}
?>

