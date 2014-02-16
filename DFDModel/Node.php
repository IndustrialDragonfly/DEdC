<?php

require_once 'Element.php';
require_once 'BadConstructorCallException.php';
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
     * This is a constructor that takes in 2 parameters.  The first parameter is 
     * always a valid storage medium.  the second paramenter is either the UUID 
     * of a Diagram or a UUID of a node decended object to load.  
     * @param {ReadStorable,WriteStorable} $datastore
     * @param string $id    the UUID of either the parent Diagram or the id of the 
     *                      Node to be loaded (can be replaced by an assocative array
     *                      to load
     * @param Mixed[] $assocativeArray
     */
    public function __construct()
    {
        if (func_num_args() == 2 )
        {
            parent::__construct();
            $this->linkList = array();
            $this->storage = func_get_arg(0);
            // Check that the first parameter implements both Readable and Writable
            if (!is_subclass_of($this->storage, "ReadStorable"))
            {
                throw new BadConstructorCallException("Passed storage object does not implement ReadStorable.");
            }
            if (!is_subclass_of($this->storage, "WriteStorable"))
            {
                throw new BadConstructorCallException("Passed storage object does not implement WriteStorable.");
            }
                        
            // Find out if handed an ID or an assocative array for the second arg
            if (is_string(func_get_arg(1)))
            {
                $id = func_get_arg(1);
                // TODO - add exception handling to getTypeFromUUID call such that it at a minimum gives 
                // information specific to this class in addition to passing the original error
                $type = $this->storage->getTypeFromUUID($id);
                // Find if the type of the second argument is Diagram, if so, its a new node
                if (is_subclass_of($type, "Diagram"))
                {
                    $this->parent = $id;
                }
                //if the type of the second argument is not a Diagram, then load from storage
                elseif (is_subclass_of($type, "Node"))
                {
                    $this->id = $id;

                    $assocativeArray = $this->storage->loadNode($this->id);

                    $this->loadAssociativeArray($assocativeArray);

                }
                else
                {
                    throw new BadConstructorCallException("Passed ID was for neither a Node nor a Diagram.");
                }
            }
            // Otherwise if it is an array, load it
            elseif (is_array(func_get_arg(1)))
            {
                $assocativeArray = func_get_arg(1);
                
                $this->loadAssociativeArray($assocativeArray);
            }
            else
            {
                throw new BadConstructorCallException("Invalid second parameter, can be neither an ID or an assocative array");
            }
        }
        else
        {
            throw new BadConstructorCallException("Invalid number of input parameters were passed to this constructor");
        }
    }

    //</editor-fold>
    //<editor-fold desc="Accessor functions" defaultstate="collapsed">

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
        // Check that it is a link, and that it isn't already in the array
        // This allows either link or node to add link, without going into
        // infinite look
        if (is_subclass_of($newLink, "Link"))
        {
            //if (!array_search($newLink->getId(), $this->linkList))
            {
                array_push($this->linkList, $newLink->getId());
            }
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
            if ($this->linkList[$i] == $linkId)
            {
                return $this->linkList[$i];
            }
        }
        return null;
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
    protected function loadAssociativeArray($associativeArray)
    {
        // TODO - error handling for missing elements/invalid elements
        // Potentially this section could be rewritten using a foreach loop
        // on the array and reflection on the current node to determine
        // what it should store locally
        parent::loadAssociativeArray($associativeArray);
        $this->linkList = $associativeArray['linkList'];
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
        
        if (is_subclass_of($link, "Link"))
        {
            var_dump($this->linkList);
            var_dump($link->getId());
            //find if the link is in the list and get its location if it is
            $loc = array_search($link->getId(), $this->linkList, True);
            var_dump($loc);
            $loc = FALSE;
            for ($i = 0; $i < count($this->linkList); $i++)
            {
                $current = $this->linkList[$i];
                if( $current['id'] == $link->getId())
                {
                    $loc = $i;
                }
            }
            
            if ($loc !== FALSE)
            if (FALSE !== array_search($link->getId(), $this->linkList, False))
            {

                //remove the link from the list
                unset($this->linkList[$loc]);
                //normalize the indexes of the list
                $this->linkList = array_values($this->linkList);
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
        for ($i = count($this->linkList); $i > 0; $i--)
        {
            $type = $this->storage->getTypeFromUUID($this->linkList[0]);
            $link = new $type($this->storage, $this->linkList[0]);
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
    }

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
