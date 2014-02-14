<?php
require_once 'Entity.php';
/**
 * Currently extends the Entity, but is empty, later should be the base object
 * which contains most of the functionality for diagram objects like 
 * DataFlowDiagram
 *
 * @author eugene
 */
abstract class Diagram extends Entity
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
     * List of all nodes contained within this Diagram and basic data to use them.
     * Stored in an associative array. Eldest anchestor will be first in the array
     * @var String[]
     */
    protected $nodeList;

    /**
     * List of all links contained within this Diagram and basic data to used them.
     * Stored in an associative array.
     * @var String[]
     */
    protected $linkList;

    /**
     * List of all the the DiaNodes contained with this Diagram and basic data
     * for the frontend to use them. Stored in an associative array.
     * @var Mixed[]
     */
    protected $diaNodeList;

    /**
     * The "parent" DiaNode connected to this Diagram
     * Can be null if root
     * @var String 
     */
    protected $diaNode;

    //</editor-fold>
    //<editor-fold desc="Constructor" defaultstate="collapsed">
    /**
     * This is a constructor that takes in a variable number of arguments: 
     * 1 parameter: create a new "root" Diagram which has no parents
     * 2 parameters: create a create a new Diagram with a parent or load a 
     * diagram from storage
     * first parameter will always be the storage and is required
     * second parameter is optional; this is either the UUID of a parent node 
     * or a UUID of a diagram to load from storage
     * @param Readable/Writable $storage
     * @param String $id this is the UUID of either the DiaNode this is 
     *                  connected to or the UUID of the DFD to load from storeage 
     */
   public function __construct()
   {
      parent::__construct();
      $this->storage = func_get_arg(0);
      $this->nodeList = array();
      $this->linkList = array();
      $this->diaNodeList = array();
      // If there is only one argument (the storage object) then this is a
      // root DFD
      // DataFlowDiagram($storage)
      if (func_num_args() == 1)
      {
         $this->ancestry = null;
         $this->diaNode = null;
      }
      // If creating a new DFD connected to a DiaNode
      // DataFlowDiagram($storage, $DiaNode)
      else if (func_num_args() == 2 )
      {
          if(  is_subclass_of($this->storage->getTypeFromUUID(func_get_arg(1)), "DiaNode")  ) 
          {
            $this->diaNode = func_get_arg(1);

             // Initialize the linked DiaNode so we can get its parent and link
             // ourselves into it
             $type = getTypeFromUUID($this->diaNode);
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
          else if ( is_subclass_of($this->storage->getTypeFromUUID(func_get_arg(1)), "Diagram") )
          {
            $this->id = func_get_arg(1);
            $vars = $this->storage->loadDFD($this->id);

            // Load up values from the associative array
            $this->label = $vars['label'];
            $this->originator = $vars['originator'];
            $this->nodeList = $vars['nodeList'];
            $this->linkList = $vars['linkList'];
            $this->diaNodeList = $vars['DiaNodeList'];
            $this->ancestry = $vars['ancestry'];
          }
          else
          {
              throw new BadConstructorCallException('UUID that was passed did not belong to a Diagram or  DiaNode');
          }
      }
      else
      {
          throw  new BadConstructorCallException('incorect number of parameters was passed to the constructor');
      }
   }

    //</editor-fold>
    //<editor-fold desc="Accessor functions" defaultstate="collapsed">
    //<editor-fold desc="linkList Accessors" defaultstate="collapsed">
    /**
     * Function that return the number of links in the list of links
     * @return int
     */
    public function getNumberOfLinks()
    {
        return count($this->linkList);
    }
    
    /**
     * This is a function that returns the list of ids for every Link in this object
     * @return String[]
     */
    public function getLinks()
    {
        return $this->linkList;
    }
    
    /**
     * This is a function that will return a specific link UUID from the list 
     * based uppon its position
     * @param int $position
     * @return String[]
     * @throws BadFunctionCallException if the position was out of bounds
     */
    public function getLink($position)
    {
        if ($position <= count($this->linkList) - 1 && $position >= 0)
        {
            return $this->linkList[$position];
        }
        else
        {
            throw new BadFunctionCallException("input parameter was out of bounds");
        }
    }

    /**
     * This is a function that adds a new Link to the list of Links
     * @param Link $link the link to be added
     */
    public function addLink($link)
    {
        //ensure that a valid link child was passed
        if (is_subclass_of($link, 'Link')  )
        {
            //add it to the list
            array_push($this->linkList, $link->getId());
        }
        else
        {
            throw new BadFunctionCallException("Input parameter not a vaild Link");
        }
    }

    /**
     * Finds and deletes the link at the given UUID from the linkList
     * @param String $linkid
     * @return boolean
     * @throws BadFunctionCallException if the input was not a link attached to this Diagram
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
            throw new BadFunctionCallException("This link was not connected to this diagram");
        }
    }

    //</editor-fold>
    //<editor-fold desc="nodeList Accessors" defaultstate="collapsed">
    /**
     * This is a function that returns the number of Nodes in this DFD
     * @return int
     */
    public function getNumberOfNodes()
    {
        return count($this->nodeList);
    }
     
    /**
     * This is a function that returns the list of ids for every node in this DFD
     * @return String[]
     */
    public function getNodes()
    {
        return $this->nodeList;
    }
    
    /**
     * This is a function that returns a specific 
     * @param type $position
     * @return type
     * @throws BadFunctionCallException
     */
    public function getNode($position)
    {
        if ($position <= count($this->nodeList) - 1 && $position >= 0)
        {
            return $this->nodeList[$position];
        }
        else
        {
            throw new BadFunctionCallException("input parameter was out of bounds");
        }
    }
    
    /**
     * This is a function that will add a node to the list of Nodes
     * @param Node $node
     * @throws BadFunctionCallException 
     */
    public function addNode($node)
    {
        //ensure that a valid Node child was passed
        if (is_subclass_of($node, 'Node')  )
        {
            //add it to the list
            array_push($this->nodeList, $node->getId());
        }
        else
        {
            throw new BadFunctionCallException("Input parameter not a vaild Node");
        }
    }
    
    /**
     * Finds and deletes the link at the given UUID from the linkList
     * @param String $nodeid
     * @return boolean
     * @throws BadFunctionCallException
     */
    public function removeNode($nodeId)
    {
        $type = $this->storage->getTypeFromUUID($nodeId);
        $node = new $type($this->storage, $nodeId);
        $node->delete();
        $loc = array_search($nodeId, $this->nodeList);
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
            throw new BadFunctionCallException("input node was not contained within this diagram");
        }
    }
    //</editor-fold>
    //<editor-fold desc="$diaNodeList Accessors" defaultstate="collapsed">
    /**
     * This is a function that returns the number of DiaNodes contained within 
     * this Diagram
     * @return int
     */
    public function getNumberOfDiaNodes()
    {
        return count($this->diaNodeList);
    }
    
    /**
     * This function returns the list of UUIDs of every $diaNode within this 
     * Diagram
     * @return String[]
     */
    public function getDiaNodes()
    {
        return $this->diaNodeList;
    }
    
    /**
     * This fucnction returns a specific $diaNode within this Diagram based 
     * upon its location in the list
     * @param int $position
     * @return String[] the UUID of the $diaNode
     * @throws BadFunctionCallException if the input was out of bounds
     */
    public function getDiaNode($position)
    {
        if ($position <= count($this->diaNodeList) - 1 && $position >= 0)
        {
            return $this->diaNodeList[$position];
        }
        else
        {
            throw new BadFunctionCallException("input parameter was out of bounds");
        }
    }
    
    /**
     * This is a function that will add an ID of a $diaNode to the list of $diaNodes
     * @param DiaNode $node the diaNode whose id is to be added
     * @throws BadFunctionCallException if you pass a variable that does not inherit from DiaNode
     */
    public function addDiaNode($node)
    {
        //ensure that a valid Node child was passed
        if (is_subclass_of($node, 'DiaNode')  )
        {
            //add it to the list
            array_push($this->diaNodeList, $node->getId());
        }
        else
        {
            throw new BadFunctionCallException("Input parameter not a vaild DiaNode");
        }
    }
    
    /**
     * Finds and deletes the DiaNode with a given UUID from the DiaNodeList
     * @param String $DiaNodeId
     * @return boolean
     * @throws BadFunctionCallException
     */
    public function removeDiaNode($DiaNodeId)
    {
        $type = $this->storage->getTypeFromUUID($DiaNodeId);
        $subDFDNode = new $type($this->storage, $DiaNodeId);
        $subDFDNode->delete();
        $loc = array_search($DiaNodeId, $this->diaNodeList);
        if ($loc !== FALSE)
        {

            //remove the link from the list
            unset($this->diaNodeList[$loc]);
            //normalize the indexes of the list
            $this->diaNodeList = array_values($this->diaNodeList);
            return true;
        }
        else
        {
            throw new BadFunctionCallException("this DiaNode was not contained within this diagram");
         }
   }
   
   
   //</editor-fold>
    //<editor-fold desc="ancestry Accessors" defaultstate="collapsed">
    /**
     * This function returns the number of ancestors of this DFD
     * @return int
     */
    public function getNumberOfAncestors()
    {
        return count($this->ancestry);
    }
    
    /**
     * This is a function that returns the immediate parent to this DFD
     * @return String
     */
    public function getParent()
    {
        return $this->ancestry[count($this->ancestry)-1];
    }
    
    /**
     * This is a function the returns the eldest parent to this node (the one 
     * whose parent would be null)
     * @return String
     */
    public function getEldestParent()
    {
        return $this->ancestry[0];
    }
    
    /**
     * This is a function that retrieves the UUID of the specified ancestor if 
     * it is in bounds
     * @param int $postion
     * @return String
     * @throws BadFunctionCallException
     */
    public function getNthAncestor($postion)
    {
        if($position < count($this->ancestry) && $postion >= 0)
        {
            return $this->ancestry[$postion];
        }
        else
        {
            throw new BadFunctionCallException("Specified postion was out of bounds");
        }
    }
    
    /**
     * This is a function that returns the entire list of ancestors starting
     * with the oldest and working back to the newest
     * @return String[]
     */
    public function getAncestry()
    {
        return $this->ancestry;
    }
    //</editor-fold>
    
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
    * DiaNodeList String[]
    * diaNode String 
    * 
    * @returns Mixed[]
    */
   public function getAssociativeArray()
   {
       // Parent Attributes
       $dfdArray = parent::getAssociativeArray();
       
       // DFD Attributes
       $dfdArray['ancestry'] = $this->ancestry;
       $dfdArray['nodeList'] = $this->nodeList;
       $dfdArray['linkList'] = $this->linkList;
       $dfdArray['DiaNodeList'] = $this->diaNodeList;
       $dfdArray['diaNode'] = $this->diaNode;
       
       return $dfdArray;
   }
    //</editor-fold>
    //<editor-fold desc="Storage functions" defaultstate="collapsed">
    /**
     * function that will save this object to the database
     * this will also save every element in the element list
     */
    public function save()
    {
        $this->storage->saveDiagram($this->id, get_class($this), $this->label, 
                $this->originator, $this->ancestry, $this->nodeList, 
                $this->linkList, $this->diaNodeList, $this->diaNode);
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
        // 
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
        // Remove its diaNodes
        foreach ($this->diaNodeList as $diaNode)
        {
            $this->deleteDiaNode(diaNode);
        }

        // Remove the remaining portions of the DFD from the database.
        // Note that this will NOT delete the children DFDs but leave them
        // orphaned instead
        $this->storage->deleteDiagram($this->id);
    }

    //</editor-fold>
}
