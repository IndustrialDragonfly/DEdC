<?php

require_once '../Entity.php';
require_once '../Multiprocess.php';
/**
 * This is an object that represents a dataflow diagram which consists of a 
 * collection of nodes and links between them
 *
 * @author Josh Clark
 * @author Eugene Davis
 */
class DataFlowDiagram extends Entity
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
        // If there is only one argument (the storage object) then this is a
        // root DFD
        // DataFlowDiagram($storage)
        if (func_num_args() == 1)
        {
            $this->elementList = array();
            $this->parentStack = null;
            $this->subDFDNode = null;
        }
        // If creating a new DFD connected to a SubDFDNode
        // DataFlowDiagram($storage, $SubDFDNode)
        else if (func_num_args() == 2 &&
                is_subclass_of($this->storage->getTypeFromUUID(func_get_arg(1)), "SubDFDNode"))
        {
            $this->elementList = array();
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
            $this->elementList = array();
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
    //</editor-fold>
    //<editor-fold desc="subDFDNodeList Accessors" defaultstate="collapsed">
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

    //</editor-fold>
    //</editor-fold>
    //<editor-fold desc="Storage functions" defaultstate="collapsed">
    /**
     * function that will save this object to the database
     * this will also save every element in the element list
     */
    public function save()
    {
        $this->storage->saveDFD($this->id, get_class($this), $this->label, $this->originator, $this->ancestry, $this->nodeList, $this->linkList, $this->subDFDNodeList, $this->subDFDNode);
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