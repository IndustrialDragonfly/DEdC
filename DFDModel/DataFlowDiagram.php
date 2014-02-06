<?php

require_once 'Entity.php';
require_once 'Multiprocess.php';
/**
* Description of DataFlowDiagram
*
* @author Josh Clark
* @author Eugene Davis
*/
class DataFlowDiagram extends Diagram
{
   //<editor-fold desc="Attributes" defaultstate="collapsed">
    /**
     * Stack of the ancestors of this DFD, starting at the root DFD
     * and going back to the immediate parent.
     * Used to figure out how to route links that connect outside of this DFD.
     * Can be null if root
     * @var String[]
     */
   protected $ancestry;
   /**
    * List of all nodes contained within this DFD and basic data to use them.
    * Stored in an associative array.
    * @var String[]
    */
   protected $nodeList;
    /**
    * List of all links contained within this DFD and basic data to used them.
    * Stored in an associative array.
    * @var String[]
    */
   protected $linkList;
   /**
    * List of all the the subDFDNodes contained with this DFD and basic data
    * for the frontend to use them. Stored in an associative array.
    * @var Mixed[]
    */
   protected $subDFDNodeList;
   /**
    * Storage object, should be readable and/or writable (depending on whether
    * this is a normal data store, import data source, or export data format)
    * @var Readable/Writable
    */
   protected $storage;
   /**
    * The SubDFDNode UUID that this DFD is linked to 
    * Can be null if root
    * @var String 
    */
   protected $subDFDNode;

   //</editor-fold>
   //<editor-fold desc="Constructor" defaultstate="collapsed">
   /**
    * Constructor. If no arguments are specified a new object is created with
    * a random id and having empty lists for elements and external links. If
    * three arguments are specified, the oject is loaded from the DB if an entry
    * with a matching id exists. If parent is null it is assumed that there are
    * no external links
    * @param Readable/Writable $storage
    * @param string $id
    */
   public function __construct()
   {
      parent::__construct();
      $this->storage = func_get_arg(0);
      $this->nodeList = array();
      $this->linkList = array();
      $this->subDFDNodeList = array();
      // If there is only one argument (the storage object) then this is a
      // root DFD
      // DataFlowDiagram($storage)
      if (func_num_args() == 1)
      {
         $this->parentStack = null;
         $this->subDFDNode = null;
      }
      // If creating a new DFD connected to a SubDFDNode
      // DataFlowDiagram($storage, $SubDFDNode)
      else if (func_num_args() == 2 && 
              is_subclass_of($this->storage->getTypeFromUUID(func_get_arg(1)), "SubDFDNode"))
      {
           $this->subDFDNode = func_get_arg(1);
           
           // Initialize the linked SubDFDNode so we can get its parent and link
           // ourselves into it
           $type = getTypeFromUUID($this->subDFDNode);
           $subDFDNode = new $type($this->storage);
           
           $parentDFD_id = $subDFDNode->getParent();
           
           // Initialize the parent DFD and get its ancenstry
           $type = getTypeFromUUID($parentDFD_id);
           $parentDFD = new $type($parentDFD_id);
           $this->ancestry = $parentDFD->getAncestry();
           // Add immediate parent to stack
           array_push($this->ancestry, $parentDFD->getId());          
      }
      // If this is an existing DFD to load, get the ID of the DFD
      // DataFlowDiagram($storage, $DFD)
      else if (func_num_args() == 2 && 
              is_subclass_of($this->storage->getTypeFromUUID(func_get_arg(1)), "DataFlowDiagram"))
      {
         $this->id = func_get_arg(1);
         $vars = $this->storage->loadDFD($this->id);
         
         // Load up values from the associative array
         $this->label = $vars['label'];
         $this->originator = $vars['originator'];
         $this->nodeList = $vars['nodeList'];
         $this->linkList = $vars['linkList'];
         $this->subDFDNodeList = $vars['subDFDNodeList'];
         $this->ancestry = $vars['ancestry'];
      }
   }

   //</editor-fold>
   //<editor-fold desc="Accessor functions" defaultstate="collapsed">
   /**
    * Finds and deletes the link at the given UUID from the linkList
    * @param String $linkid
    * @return boolean
    * @throws BadFunctionCallException
    */
   public function removeLink($linkid)
   {
       $type = $this->storage->getTypeFromUUID($linkid);
       $link = new $type($this->storage, $linkid);
       $link->delete();
       $loc = array_search($linkid, $this->linkList);
         if ($loc !== FALSE)
         {
            
            //remove the link from the list
            unset($this->linkList[$loc]);
            //normalize the indexes of the list
            $this->linkList = array_values($this->linkList);
            return true;
         }
         else
         {
            throw new BadFunctionCallException("Input parameter not contained in DFD");
         }
   }   
   /**
    * Finds and deletes the link at the given UUID from the linkList
    * @param String $nodeid
    * @return boolean
    * @throws BadFunctionCallException
    */
   public function removeNode($nodeid)
   {
       $type = $this->storage->getTypeFromUUID($nodeid);
       $node = new $type($this->storage, $nodeid);
       $node->delete();
       $loc = array_search($nodeid, $this->nodeList);
         if ($loc !== FALSE)
         {
            
            //remove the node from the list
            unset($this->nodeList[$loc]);
            //normalize the indexes of the list
            $this->nodeList = array_values($this->nodeList);
            return true;
         }
         else
         {
            throw new BadFunctionCallException("Input parameter not contained in DFD");
         }
   }
   
    /**
    * Finds and deletes the subDFDNode at the given UUID from the subDFDNode
    * @param String $subdfdnodeid
    * @return boolean
    * @throws BadFunctionCallException
    */
   public function removesubDFDNode($subdfdnodeid)
   {
       $type = $this->storage->getTypeFromUUID($subdfdnodeid);
       $subDFDNode = new $type($this->storage, $subdfdnodeid);
       $subDFDNode->delete();
       $loc = array_search($subdfdnodeid, $this->subDFDNodeList);
         if ($loc !== FALSE)
         {
            
            //remove the link from the list
            unset($this->subDFDNodeList[$loc]);
            //normalize the indexes of the list
            $this->subDFDNodeList = array_values($this->subDFDNodeList);
            return true;
         }
         else
         {
            throw new BadFunctionCallException("Input parameter not contained in DFD");
         }
   }
   
   /**
    * Returns an assocative array representing the DFD object. This assocative
    * array has the following elements and types:
    * id String
    * label String
    * originator String
    * organization String 
    * type String
    * genericType String
    * ancestry String[]
    * nodeList String[]
    * linkList String[]
    * subDFDNodeList String[]
    * subDFDNode String 
    * 
    * @returns Mixed[]
    */
   public function getAssociativeArray()
   {
       // Parent Attributes
       $dfdArray = parent::getAssocativeArray();
       
       // DFD Attributes
       $dfdArray['ancestry'] = $this->ancestry;
       $dfdArray['nodeList'] = $this->nodeList;
       $dfdArray['linkList'] = $this->linkList;
       $dfdArray['subDFDNodeList'] = $this->subDFDNodeList;
       $dfdArray['subDFDNode'] = $this->subDFDNode;
   }
   //</editor-fold>
   //<editor-fold desc="Storage functions" defaultstate="collapsed">
   /**
* function that will save this object to the database
* this will also save every element in the element list
*/
   public function save()
   {
      $this->storage->saveDFD($this->id, get_class($this), $this->label, $this->originator, $this->ancestry, 
            $this->nodeList, $this->linkList, $this->subDFDNodeList, $this->subDFDNode);
   }

   /**
    * This function updates the data store. Currently unoptimized, as it just
    * calls delete then save.
    */
   public function update()
   {
       $this->delete();
       $this->save();
   }
   
   public function delete()
   {
       // Start by constructing all elements contained within and then deleting them.

        // Remove its links
        foreach ($this->linkList as $link)
        {
            $this->removeLink($link);
        }
        // Remove its nodes
        foreach ($this->nodeList as $node)
        {
            $this->removeNode($node);
        }
        // Remove its subDFDNodes
        foreach ($this->subDFDNodeList as $subDFDNode)
        {
            $this->removesubDFDNode($subDFDNode);
        }
        
        // Remove the remaining portions of the DFD from the database.
        // Note that this will NOT delete the children DFDs but leave them
        // orphaned instead
       $this->storage->deleteDFD($this->id);
   }
   //</editor-fold>
}
?>