<?php

/**
 * Currently extends the Entity, but is empty, later should be the base object
 * which contains most of the functionality for diagram objects like 
 * DataFlowDiagram
 *
 * @author Eugene Davis
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
     * associative array:
     * 'id'
     * 'label'
     * 'originator'
     * 'x'
     * 'y'
     * 'type'
     */
    protected $nodeList;

    /**
     * List of all links contained within this Diagram and basic data to used them.
     * Stored in an associative array.
     * @var String[]
     * associative array:
     * 'id'
     * 'label'
     * 'originator'
     * 'x'
     * 'y'
     * 'type'
     * 'originNode'
     * 'destinationNode'
     * 
     */
    protected $linkList;

    /**
     * List of all the the DiaNodes contained with this Diagram and basic data
     * for the frontend to use them. Stored in an associative array.
     * @var Mixed[]
     * associative array:
     * 'id'
     * 'label'
     * 'originator'
     * 'x'
     * 'y'
     * 'type'
     * 'childDiagramId'
     */
    protected $diaNodeList;

    /**
     * The "parent" DiaNode connected to this Diagram
     * Can be null if root
     * @var String 
     */
    protected $parentDiaNode;

    //</editor-fold>
    //<editor-fold desc="Constructor" defaultstate="collapsed">
    /**
     * This is a constructor that takes in a variable number of arguments: 
     * 1 parameter: create a new "root" Diagram which has no parents
     * 2 parameters: create a create a new Diagram with a parent or load a 
     * diagram from storage or load from an assocative array
     * first parameter will always be the storage and is required
     * second parameter is optional; this is either the UUID of a parent DiaNode 
     * or a UUID of a diagram to load from storage, or it is an associative array
     * @param {Read,Write}Storable $storage
     * @param String $id ID of a Diagram or DiaNode to link to (optionial if an assocative array is in its place)
     * @param Mixed[] $associativeArray associative array representing a diagram object (optional if an ID is in its place) 
     */
   public function __construct()
   {
        //if only a storage medium is passed create an empty root Diagram
        if(func_num_args() == 1)
        {
            parent::__construct(func_get_arg(0));
            $this->ancestry = array();
            $this->parentDiaNode = null;
            $this->diaNodeList = array();
            $this->linkList = array();
            $this->nodeList = array();
            $this->save();
                    
        }
        
        //if 2 things were passed we are either loading a Diagram from storage, 
        //or loading from a associative array, or we are creating a new mostly 
        //empty Diagram with a specified parent
        else if (func_num_args() == 2)
        {
            //the second parameter was an ID of either a Diagram to be loaded or DiaNode which will be the parent
            if(is_string(func_get_arg(1)))
            {
                
                $type = func_get_arg(0)->getTypeFromUUID(func_get_arg(1));
                //if the id belonged to a Diagram object load it
                if (is_subclass_of($type, "Diagram"))
                {
                    $this->id = func_get_arg(1);
                    $this->storage = func_get_arg(0);
                    if (!is_subclass_of($this->storage, "ReadStorable"))
                    {
                        throw new BadConstructorCallException("Passed storage object does not implement ReadStorable.");
                    }
                    if (!is_subclass_of($this->storage, "WriteStorable"))
                    {
                        throw new BadConstructorCallException("Passed storage object does not implement WriteStorable.");
                    }
                    $assocativeArray = $this->storage->loadDiagram(func_get_arg(1));
                    $this->loadAssociativeArray($assocativeArray);
                }
                //second parameter was an id of a DiaNode object create an empty 
                //Diagram with that diaNode as its parent and set up its ancestry 
                //to be the ancestry of the parent Diagrams and then add the parent 
                //Diagram to this new Diagram's ancestry.  Next set the subDiagram 
                //in the parentDiaNode to be this Diagram
                else if (is_subclass_of($type, "DiaNode"))
                {
                    //call the parent constructor and set the linkList to be an empty list
                    parent::__construct(func_get_arg(0));
                    $this->linkList = array();
                    $this->diaNodeList = array();
                    $this->nodeList = array();
                    $this->parentDiaNode = func_get_arg(1);
                    
                    //to set the ancestry load the parent Diagram and then set this object's ancestry equal to it then add the parent Diagram to it
                    //load the parent DiaNode
                    $type = $this->storage->getTypeFromUUID($this->parentDiaNode);
                    $parentDiaNode = new $type(func_get_arg(0), $this->parentDiaNode);
                    
                    //get the id of parent Diagram of the parentDiaNode
                    $parentDiagramId = $parentDiaNode->getParent();
                    
                    //load the parentDiagram
                    $type = $this->storage->getTypeFromUUID($parentDiagramId);
                    $parentDiagram = new $type(func_get_arg(0), $parentDiagramId);
                    
                    //set this Diagram's ancestry to the parentDiagrams, then add it to the list
                    $this->ancestry = $parentDiagram->getAncestry();
                    array_push($this->ancestry, $parentDiagram->getId());
                    
                    //save this new Diagram
                    $this->save();
                    
                    //set the parent DiaNode's subDiagram to be this Diagram
                    $parentDiaNode->setSubDiagram($this->getId());
                }
                else
                {
                    throw new BadConstructorCallException("the ID passed to the Diagram constructor was neither a Diagram nor a DiaNode");
                }
           }
           //if the second parameter was an associative array pass the parameters on to the parent constructor
           else if(is_array(func_get_arg(1)))
           {
               parent::__construct(func_get_arg(0), func_get_arg(1));
               $this->save();
           }
           else
           {
               throw new BadConstructorCallException("The second parameter passed to the Diagram constructor with neither an Id nor an assocaitive array");
           }
       }
       else
       {
           throw new BadConstructorCallException("An incorrect number of parameters were passed to the constructor for Diagram");
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
    public function addLink($newLink)
    {
        //ensure that a valid link child was passed
        if (is_subclass_of($newLink, 'Link')  )
        {
            //add it to the list
            $link['id'] = $newLink->getId();
            $link['label'] = $newLink->getLabel();
            $link['originator'] = $newLink->getOriginator();
            $link['originNode'] = $newLink->getOriginNode()['id'];
            $link['destinationNode'] = $newLink->getDestinationNode()['id'];
            $link['type'] = get_class($newLink);
            
            array_push($this->linkList, $link);
            $this->update();
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
        //$loc = array_search($linkid, $this->linkList);
        $loc = FALSE;
        for ($i = 0; $i < count($this->linkList); $i++)
        {
            $current = $this->linkList[$i];
            if( $current['id'] == $linkid)
            {
                $loc = $i;
            }
        }
        if ($loc !== FALSE)
        {
            //remove the link from the list
            unset($this->linkList[$loc]);
            //normalize the indexes of the list
            $this->linkList = array_values($this->linkList);
            $this->update();
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
    public function addNode($newNode)
    {
        //ensure that a valid Node child was passed
        if (is_subclass_of($newNode, 'Node')  )
        {
            //add it to the list
            $node['id'] = $newNode->getId();
            $node['label'] = $newNode->getLabel();
            $node['originator'] = $newNode->getOriginator();
            $node['x'] = $newNode->getX();
            $node['y'] = $newNode->getY();
            $node['type'] = get_class($newNode);
            
            array_push($this->nodeList, $node);
            $this->update();
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
        //$loc = array_search($nodeId, $this->nodeList);
        //find the location of the id in the list of nodes
        $loc = FALSE;
        for ($i = 0; $i < count($this->nodeList); $i++)
        {
            $current = $this->nodeList[$i];
            if( $current['id'] == $nodeId)
            {
                $loc = $i;
            }
        }
        if ($loc !== FALSE)
        {

            //remove the node from the list
            unset($this->nodeList[$loc]);
            //normalize the indexes of the list
            $this->nodeList = array_values($this->nodeList);
            $this->update();
            return true;
        }
        else
        {
            throw new BadFunctionCallException("input node was not contained within this diagram");
        }
    }
    //</editor-fold>
    //<editor-fold desc="diaNodeList Accessors" defaultstate="collapsed">
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
     * @param DiaNode $newNode the diaNode whose id is to be added
     * @throws BadFunctionCallException if you pass a variable that does not inherit from DiaNode
     */
    public function addDiaNode($newNode)
    {
        //ensure that a valid Node child was passed
        if (is_subclass_of($newNode, 'DiaNode')  )
        {
            //add it to the list
            $node['id'] = $newNode->getId();
            $node['label'] = $newNode->getLabel();
            $node['originator'] = $newNode->getOriginator();
            $node['x'] = $newNode->getX();
            $node['y'] = $newNode->getY();
            $node['type'] = get_class($newNode);
            $node['childDiagramId'] = $newNode->getSubDiagram();
            
            array_push($this->diaNodeList, $node);
            $this->update();
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
        //$loc = array_search($DiaNodeId, $this->diaNodeList);
        $loc = FALSE;
        for ($i = 0; $i < count($this->diaNodeList); $i++)
        {
            $current = $this->diaNodeList[$i];
            if( $current['id'] == $DiaNodeId)
            {
                $loc = $i;
            }
        }
        if ($loc !== FALSE)
        {

            //remove the link from the list
            unset($this->diaNodeList[$loc]);
            //normalize the indexes of the list
            $this->diaNodeList = array_values($this->diaNodeList);
            $this->update();
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
    //<editor-fold desc="ParentDiaNode functions" defaultstate="collapsed">
    /**
     * This is a function that will return the ID of the DiaNode that contains this Diagram
     * @return String
     */
    public function getParentDiaNode()
    {
        return $this->parentDiaNode;
    }
    
    /**
     * This is a function that will set the ID of the DiaNode that contains this Diagram
     * @param String $newParentDiaNodeID
     */
    protected function setParentDiaNode($newParentDiaNodeID)
    {
        if($this->parentDiaNode != $newParentDiaNodeID)
        {
            $this->parentDiaNode = $newParentDiaNodeID;
            $type = $this->storage->getTypeFromUUID($newParentDiaNodeID);
            if(is_subclass_of($type, "DiaNode"))
            {
            $diaNode = new $type($this->storage, $newParentDiaNodeID);
            $diaNode->setSubDiagram($this->getId());
            $this->update();
            }
            else
            {
                throw new BadFunctionCallException("ID did not belong to a valid DiaNode");
            }
        }
        
    }
    //</editor-fold>
    //<editor-fold desc="Associative Array functions" defaultstate="collapsed">
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
       $dfdArray['diaNode'] = $this->parentDiaNode;
       
       return $dfdArray;
   }
   
    /**
     * Takes an assocative array representing an object and loads it into this
     * object.
     * @param Mixed[] $assocativeArray
     */
    public function loadAssociativeArray($associativeArray)
    {
        // Potentially this section could be rewritten using a foreach loop
        // on the array and reflection on the current node to determine
        // what it should store locally
        parent::loadAssociativeArray($associativeArray);
        if(isset($associativeArray['nodeList']))
        {
            $this->nodeList = $associativeArray['nodeList'];
        }
        else
        {
            $this->nodeList = Array();
        }
        
        if(isset($associativeArray['linkList']))
        {
            $this->linkList = $associativeArray['linkList'];
        }
        else
        {
            $this->linkList = Array();
        }
        
        if(isset($associativeArray['DiaNodeList']))
        {
            $this->diaNodeList = $associativeArray['DiaNodeList'];
        }
        else
        {
            $this->diaNodeList = Array();
        }
        
        if(isset($associativeArray['diaNode']))
        {
            $this->parentDiaNode = $associativeArray['diaNode'];
        }
        else
        {
            $this->parentDiaNode = null;
        }
        
        if(isset($associativeArray['ancestry']))
        {
            $this->ancestry = $associativeArray['ancestry'];
        }
        else
        {
            $this->ancestry = Array();
        }
    }
    //</editor-fold>
    //</editor-fold>
    //<editor-fold desc="Storage functions" defaultstate="collapsed">
    /**
     * function that will save this object to the database
     * this will also save every element in the element list
     */
    public function save()
    {
        $this->storage->saveDiagram($this->id, get_class($this), $this->label, 
                $this->user, $this->ancestry, $this->nodeList, 
                $this->linkList, $this->diaNodeList, $this->parentDiaNode);
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
            //$this->removeLink($link['id']);
            $this->storage->deleteLink($link['id']);
        }
        // Remove its nodes
        foreach ($this->nodeList as $node)
        {
            //$this->removeNode($node['id']);
            $this->storage->deleteNode($node['id']);
        }
        // Remove its diaNodes
        foreach ($this->diaNodeList as $diaNode)
        {
            //$this->removeDiaNode($diaNode['id']);
            $this->storage->deleteDiaNode($diaNode['id']);
            $this->storage->deleteNode($diaNode['id']);
        }

        // Remove the remaining portions of the DFD from the database.
        // Note that this will NOT delete the children DFDs but leave them
        // orphaned instead
        $this->storage->deleteDiagram($this->id);
    }
    
    public function refresh()
    {
        $associativeArray = $this->storage->loadDiagram($this->id);
        $this->loadAssociativeArray($associativeArray);
    }

    //</editor-fold>
}
