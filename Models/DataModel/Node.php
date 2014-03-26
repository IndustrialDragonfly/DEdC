<?php

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
    /**
     * This is a container which holds the UUIDs of eevry link coming out from 
     * this node
     * @var String[]
     */
    protected $linkList;

    //</editor-fold>
    
    //<editor-fold desc="Constructor" defaultstate="collapsed">
    /**
     * This is the constructor for the Node Class.  It takes in 2 parameters; 
     * the first is always a valid storage medium, the second one is either an 
     * id or an associative array.  If the second parameter is an id of a valid 
     * node subclass the node is loaded in from the storage medium.  If the 
     * second parameter was an id of anything else it is passed to the 
     * constructor for Element for it to handle.  If the second parameter is an 
     * associative array it is likewise passed to the Element constructor for it
     *  to handle.  
     * @param {ReadStorable,WriteStorable} $datastore
     * @param string $id    the UUID of either the parent Diagram or the id of the 
     *                      Node to be loaded (can be replaced by an assocative array
     *                      to load
     * @param Mixed[] $assocativeArray
     */
    public function __construct()
    {
        //check number of paramenters
        if(func_num_args() == 2)
        {
            //check type of second parameter
            if (is_string(func_get_arg(1)))
            {
                //if second parameter is an id of a node subclass object
                $type = func_get_arg(0)->getTypeFromUUID(func_get_arg(1));
                if (is_subclass_of($type, "Node"))
                {
                    $this->id = func_get_arg(1);
                    // Storage is set here, as entity (parent) is never called
                    $this->storage = func_get_arg(0);
                    if (!is_subclass_of($this->storage, "ReadStorable"))
                    {
                        throw new BadConstructorCallException("Passed storage object does not implement ReadStorable.");
                    }
                    if (!is_subclass_of($this->storage, "WriteStorable"))
                    {
                        throw new BadConstructorCallException("Passed storage object does not implement WriteStorable.");
                    }
                    $assocativeArray = $this->storage->loadNode(func_get_arg(1));
                    $this->loadAssociativeArray($assocativeArray);
                }
                //second parameter was an id of a diagram object so call the higher constructor and add this object to the nodeList of that Diagram
                else if (is_subclass_of($type, 'Diagram'))
                {
                    //call the parent constructor and set the linkList to be an empty list
                    parent::__construct(func_get_arg(0), func_get_arg(1));
                    $this->linkList = array();
                    $this->save();
                    //add this node to the parent diagram's node list
                    //$theDiagram = new $type($this->storage, func_get_arg(1));
                    //$theDiagram->addNode($this);
                    //$theDiagram->update();
                }
                //the second parameter did not decend from a Node or a Diagram object
                else
                {
                    throw new BadConstructorCallException("The id passed to the Node Constructor was neither a valid Node or Diagram decended object");
                }
            }
            //second parameter should be an associative array so pass it along to Element's constructor
            else
            {
                parent::__construct(func_get_arg(0), func_get_arg(1));
                $this->save();
            }
        }
        else
        {
            throw new BadConstructorCallException("An incorrect number of parameters were passed to the Node constructor");
        }
    }

    //</editor-fold>
    
    //<editor-fold desc="Accessor functions" defaultstate="collapsed">
    //<editor-fold desc="Link functions" defaultstate="collapsed">

    /**
     * This is a function that gets the number of links that connect to this node
     * @return int the number of links 
     */
    public function getNumberOfLinks()
    {
        return count($this->linkList);
    }

    /**
     * Function that returns the links that connect to this node
     * @return string[] an array of all the uuid of the links to this node
     */
    public function getLinks()
    {
        return $this->linkList;
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
        //ensure that this is only called by a Link object
        $trace=debug_backtrace();
        $caller=array_shift($trace);
        $caller=array_shift($trace);
        if($caller['class'] != 'Link')
        {
            throw new BadFunctionCallException("addLink() was not called by a Link object");
        }
        
        // Check that it is a link
        if (is_subclass_of($newLink, "Link"))
        {
            //create an new associative array and add it to the list
            $link['id']  = $newLink->getId();
            $link['label'] = $newLink->getLabel();
            array_push($this->linkList, $link);
            $this->update();
        }
        else
        {
            throw new BadFunctionCallException("Input parameter was not a Link");
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
        if ($index <= count($this->linkList) - 1 && $index >= 0)
        {
            return $this->linkList[$index];
        }
        else
        {
            throw new BadFunctionCallException("input parameter was out of bounds");
        }
    }

    /**
     * This is a function that searches the list of DataFlows and returns one 
     * with a matching id
     * @param type $linkId
     * @return a UUID of a DataFlow that has an id matching the specified one, will return 
     *          null if not specified
     */
    public function getLinkbyId($linkId)
    {
        for ($i = 0; $i < count($this->linkList); $i++)
        {
            if ($this->linkList[$i]['id'] == $linkId)
            {
                return $this->linkList[$i];
            }
        }
        return null;
    }
    
    
    /**
     * removes a specified DataFlow from the list of links
     * Should only be called by Link object
     * @param type $link the link to be removed
     * @return boolean if the link was in the array
     * @throws BadFunctionCallException if the input was not a DataFlow]
     */
    public function removeLink($link)
    {    	 
        //ensure that this is only called by a Link object
        $trace=debug_backtrace();
        $caller=array_shift($trace);
        $caller=array_shift($trace);
        if($caller['class'] != 'Link')
        {
            throw new BadFunctionCallException("removeLink() was not called by a Link object");
        }
        
        if (is_subclass_of($link, "Link"))
        {

            //find if the link is in the list and get its location if it is in the array
            //$loc = array_search($link->getId(), $this->linkList, True);
            $loc = FALSE;
            for ($i = 0; $i < count($this->linkList); $i++)
            {
                $current = $this->linkList[$i];
                if( $current['id'] == $link->getId())
                {
                    $loc = $i;
                }
            }
            //if the Link was found remove it
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
                throw new BadFunctionCallException("Input parameter not contained in this Node");
            }
        }
        else
        {
            throw new BadFunctionCallException("Input parameter was not descended from Link");
        }
    }

    /**
     * This function that removes every link to this node, used when deleting a node
     */
    public function removeAllLinks()
    {
        // Counts down to avoid any ambigutity with unsetting of things in
        // links
        for ($i = count($this->linkList)-1; $i >= 0; $i--)
        {
            $type = $this->storage->getTypeFromUUID($this->linkList[$i]['id']);
            $link = new $type($this->storage, $this->linkList[$i]['id']);
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
        unset($this->linkList);
        $this->linkList = array();
        $this->update();
    }
    //</editor-fold>
    //<editor-fold desc="AssociativeArray functions" defaultstate="collapsed">
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
     * diagramId String
     * linkList String[][]
     * 
     * @return Mixed[]
     */
    public function getAssociativeArray()
    {
        $nodeArray = parent::getAssociativeArray();
        $nodeArray['linkList'] = $this->linkList;

        return $nodeArray;
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
        if( isset($associativeArray['linkList']))
        {
            $this->linkList = $associativeArray['linkList'];
        }
        else
        {
            $this->linkList = array();
        }
    }

    //</editor-fold>
    //</editor-fold>

    //<editor-fold desc="Save/Delete/Update" defaultstate="collapsed">
    /**
     * function that will save this object to the data store
     * 
     * @param WriteStorable $dataStore
     */
    public function save()
    {
        $this->storage->saveNode($this->id, $this->label, get_class($this), $this->originator, $this->x, $this->y, $this->linkList, $this->getNumberOfLinks(), $this->parent);
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
