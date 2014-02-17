<?php

require_once 'Element.php';
require_once 'Constants.php';
/**
 * Abstract class from which dataflows and similar objects will inherit
 * from
 *
 * @author Josh Clark
 * @author Eugene Davis
 */
abstract class Link extends Element
{
//<editor-fold desc="Attributes" defaultstate="collapsed">
    /**
     * UUID of a node object
     * @var String
     */
    protected $originNode;

    /**
     * UUID of a node object
     * @var String
     */
    protected $destinationNode;

    //</editor-fold>
//<editor-fold desc="Constructor" defaultstate="collapsed">
    /**
     * constructor. if no arguments are specified a new object is created with
     * a random id. if three arguments are specified, the oject is loaded from the
     * DB if an entry with a matching id exists
     * @param {Read,Write}Storable $storage
     * @param string $id (Optional if associative array is in its place)
     * @param Mixed[] $associativeArray (Optional if a Link or Diagram ID is in its place)
     */
    public function __construct()
    {  
        if (func_num_args() == 2 )
        {
             parent::__construct(func_get_arg(0), func_get_arg(1));
            // TODO - Get storage setting moved up the stack so it isn't repeated all over the place
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
                    $this->originNode = NULL;
                    $this->destinationNode = NULL;
                }
                //if the type of the second argument is not a Diagram, then load from storage
                elseif (is_subclass_of($type, "Link"))
                {                    
                    $associativeArray = $this->storage->loadLink($this->id);

                    $this->loadAssociativeArray($associativeArray);

                }
                else
                {
                    throw new BadConstructorCallException("Passed ID was for neither a Link nor a Diagram.");
                }
            }
            // Otherwise if it is an array, load it
            // TODO - figure out if this can be called at a higher level (e.g. entity) while still using the entire chain of load functions
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
//<editor-fold desc="originNode functions" defaultstate="collapsed">
    /**
     * function that returns the Node that this dataflow originates from
     * @return String 
     */
    public function getOriginNode()
    {
        return $this->originNode;
    }

    /**
     * function that sets the origin node to the specified node and adds itself to thats nodes list of links,
     * if the origin node was already set it will first remove itself from that Nodes list of Links
     * @param Node $aNode 
     * @throws BadFunctionCallException if the input was not a Node object
     */
    public function setOriginNode($aNode)
    {
        //make sure a Node object was passed
        if ($aNode instanceof Node)
        {
            //if origin has not been set yet
            if ($this->originNode == NULL)
            {
                //set the origin node and add this DataFlow to its list of Links
                $this->originNode = $aNode->getId();
                $aNode->addLink($this);
                $aNode->update();
            }
            //if the origin node has already been set
            else
            {
                //remove this DataFlow from the old origin nodes list of links and 
                //thenset the origin node to the new node and add this DataFlow to 
                //its list of Links
                $type = $this->storage->getTypeFromUUID($this->originNode);
                $node = new $type($this->storage, $this->originNode);
                $node->removeLink($this);
                $node->update();

                $this->originNode = $aNode->getId();
                $aNode->addLink($this);
                $aNode->update();
            }
        }
        else
        {
            throw new BadFunctionCallException("input parameter was not a Node");
        }
    }

    /**
     * function that clears the origin node and removes it from the list of links of its old origin node
     */
    public function clearOriginNode()
    {
        if ($this->originNode != NULL)
        {
            $type = $this->storage->getTypeFromUUID($this->originNode);
            $node = new $type($this->storage, $this->originNode);
            $node->removeLink($this);
            $this->originNode = NULL;
            $node->update();
        }
    }

    //</editor-fold>
//<editor-fold desc="destinationNode functions" defaultstate="collapsed">
    /**
     * function that returns the Node that this dataflow ends at
     * @return Node 
     */
    public function getDestinationNode()
    {
        return $this->destinationNode;
    }

    /**
     * function that sets the destination node to the specified node and adds itself to thats nodes list of links,
     * if the destination node was already set it will first remove itself from that Nodes list of Links
     * @param Node $aNode 
     * @throws BadFunctionCallException if the input was not a Node object
     */
    public function setDestinationNode($aNode)
    {
        //make sure a Node object was passed
        if (is_subclass_of($aNode, "Node"))
        {
            //if destination has not been set yet
            if ($this->destinationNode == NULL)
            {
                //set the destination node and add this DataFlow to its list of Links
                $this->destinationNode = $aNode->getId();
                $aNode->addLink($this);
                $aNode->update();
            }
            //if the destination node has already been set
            else
            {
                //remove this DataFlow from the old destination nodes list of links and 
                //then set the destination node to the new node and add this DataFlow to 
                //its list of Links
                $type = $this->storage->getTypeFromUUID($this->destinationNode);
                $node = new $type($this->storage, $this->destinationNode);
                $node->removeLink($this);
                $node->update();

                $this->destinationNode = $aNode->getId();
                $aNode->addLink($this);
                $node->update();
            }
        }
        else
        {
            throw new BadFunctionCallException("input parameter was not a Node");
        }
    }

    /**
     * function that clears the origin node and removes it from the list of links of its old origin node
     */
    public function clearDestinationNode()
    {
        if ($this->destinationNode != NULL)
        {
            $type = $this->storage->getTypeFromUUID($this->destinationNode);
            $node = new $type($this->storage, $this->destinationNode);
            $node->removeLink($this);
            $node->update();
            $this->destinationNode = NULL;
        }
    }

    //</editor-fold>

    /**
     * Removes the specified node
     * Follows a Link-centric breaking approach - that is the link removes
     * itself from the node, rather than the node removing itself from the
     * link
     * 
     * @param Node $node
     */
    public function removeNode($node)
    {
        if ($node->getId() == $this->getOriginNode())
        {
            $this->clearOriginNode();
            // Actually call back the node that just called and remove the node
            // since Links ALWAYS break the connection off
            //$node->removeLink($this);
            //$node->update();
        }
        elseif ($node->getId() == $this->getDestinationNode())
        {
            $this->clearDestinationNode();
            //$node->removeLink($this);
            //$node->update();
        }
        else
        {
            // Throw exception
            throw new BadFunctionCallException('passed a node that is not connected to a ');
        }
    }

    /**
     * function that removes all of the connections to this DataFlow
     */
    public function removeAllNodes()
    {
        $this->clearOriginNode();
        $this->clearDestinationNode();
    }

    /**
     * Returns an assocative array representing the link object. This 
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
     * originNode String
     * destinationNode String
     * 
     * @return Mixed[]
     */
    public function getAssociativeArray()
    {
        // Get Entity and Element array
        $linkArray = parent::getAssociativeArray();

        // Add Link Attributes to array
        $linkArray['originNode'] = $this->originNode;
        $linkArray['destinationNode'] = $this->destinationNode;

        return $linkArray;
    }
    
    /**
     * Takes an assocative array representing an object and loads it into this
     * object.
     * @param Mixed[] $assocativeArray
     */
    protected function loadAssociativeArray($associativeArray)
    {
        parent::loadAssociativeArray($associativeArray);
        // TODO - error handling for missing elements/invalid elements
        $this->originNode = $associativeArray['originNode'];
        $this->destinationNode = $associativeArray['destinationNode'];
        
    }

    //</editor-fold>
//<editor-fold desc="Data Store Actions" defaultstate="collapsed">
    /**
     * function that will save this object to the database
     * @param WriteStorable $datastore this is the data store to write to
     */
    public function save()
    {
        // Send info required to save dataflow to the data store
        $this->storage->saveLink($this->id, $this->label, get_class($this), $this->originator, $this->x, $this->y, $this->originNode, $this->destinationNode, $this->parent);
    }

    /**
     * Deletes the link object from the data store
     * 
     * @param Writable $datastore
     */
    public function delete()
    {
        $this->removeAllNodes();
        $this->storage->deleteLink($this->id);
    }

    /**
     * Updates the link object in the data store
     * 
     * @param Writable $datastore
     */
    public function update()
    {
        // Temporary cheaty way, should see if a more effictient way is
        // available
        $this->storage->deleteLink($this->id); // Cannot have removeAllNodes called
        $this->save();
    }

    //</editor-fold>
}
?>
