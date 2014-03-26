<?php

/**
 * Abstract class which implementes the conections between Node objects
 *
 * @author Josh Clark
 * @author Eugene Davis
 */
abstract class Link extends Element
{
//<editor-fold desc="Attributes" defaultstate="collapsed">
    /**
     * UUID of a node object
     * @var []
     * format:
     * ['id'] id of the origin node
     * ['label'] the label of the origin node
     */
    protected $originNode;

    /**
     * UUID of a node object
     * @var []
     * format:
     * ['id'] id of the destination node
     * ['label'] the label of the destination node
     */
    protected $destinationNode;

    //</editor-fold>
//<editor-fold desc="Constructor" defaultstate="collapsed">
    /**
     * This is the constructor for the Link Class.  It takes in 2 parameters; 
     * the first is always a valid storage medium, the second one is either an 
     * ID or an associative array.  If the second parameter is an id of a valid 
     * Link subclass the Link is loaded in from the storage medium.  If the 
     * second parameter was an ID of anything else it is passed to the 
     * constructor for Element for it to handle.  If the second parameter is an 
     * associative array it is likewise passed to the Element constructor for it
     *  to handle.  
     * @param {ReadStorable,WriteStorable} $datastore
     * @param string $id    the UUID of either the parent Diagram or the id of the 
     *                      Link to be loaded 
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
                //if second parameter is an id of a link subclass object
                $type = func_get_arg(0)->getTypeFromUUID(func_get_arg(1));
                if (is_subclass_of($type, "Link"))
                {
                    $this->id = func_get_arg(1);
                    // Never calling parent, so must handle setting up the storage
                    $this->storage = func_get_arg(0);
                    if (!is_subclass_of($this->storage, "ReadStorable"))
                    {
                        throw new BadConstructorCallException("Passed storage object does not implement ReadStorable.");
                    }
                    if (!is_subclass_of($this->storage, "WriteStorable"))
                    {
                        throw new BadConstructorCallException("Passed storage object does not implement WriteStorable.");
                    }
                    $assocativeArray = $this->storage->loadLink(func_get_arg(1));
                    $this->loadAssociativeArray($assocativeArray);
                }
                //second parameter was an id of a Diagram object
                else if (is_subclass_of($type, "Diagram"))
                {
                    //call the parent constructor and set the linkList to be an empty list
                    parent::__construct(func_get_arg(0), func_get_arg(1));
                    $this->originNode = NULL;
                    $this->destinationNode = NULL;
                    $this->save();
                }
                else
                {
                    throw new BadConstructorCallException("The id passed to the Link Constructor was neither a valid Link or Diagram decended object");
                }
            }
            //second parameter should be an associative array so pass it along to Element's constructor
            // TODO Check if the Link is already in the database, and call save/update accordingly
            else
            {
                parent::__construct(func_get_arg(0), func_get_arg(1));
				$associativeArray = $this->getAssociativeArray();
				if ($associativeArray ['originNode'] != NULL) 
				{
					// New node
					$newType = $this->storage->getTypeFromUUID ( $associativeArray ['originNode'] ['id'] );
					$newNode = new $newType ( $this->storage, $associativeArray ['originNode'] ['id'] );
					$newNode->addLink ( $this );
				}
				
				if ($associativeArray ['destinationNode'] != NULL) 
				{
					// New node
					$newType = $this->storage->getTypeFromUUID ( $associativeArray ['destinationNode'] ['id'] );
					$newNode = new $newType ( $this->storage, $associativeArray ['destinationNode'] ['id'] );
					$newNode->addLink ( $this );
				}
				
				$this->save ();				
            }
        }
        else
        {
            throw new BadConstructorCallException("An incorrect number of parameters were passed to the Link constructor");
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
                $this->originNode['id'] = $aNode->getId();
                $this->originNode['label'] = $aNode->getLabel();
                
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

                $this->originNode['id'] = $aNode->getId();
                $this->originNode['label'] = $aNode->getLabel();
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
            $type = $this->storage->getTypeFromUUID($this->originNode['id']);
            $node = new $type($this->storage, $this->originNode['id']);
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
                $this->destinationNode['id'] = $aNode->getId();
                $this->destinationNode['label'] = $aNode->getLabel();
                
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

                $this->destinationNode['id'] = $aNode->getId();
                $this->destinationNode['label'] = $aNode->getLabel();
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
            $type = $this->storage->getTypeFromUUID($this->destinationNode['id']);
            $node = new $type($this->storage, $this->destinationNode['id']);
            $node->removeLink($this);
            $node->update();
            $this->destinationNode = NULL;
        }
    }

    //</editor-fold>
//<editor-fold desc="generic Node functions" defaultstate="collapsed">
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
        if ($node->getId() == $this->getOriginNode()['id'])
        {
            $this->clearOriginNode();
            // Actually call back the node that just called and remove the node
            // since Links ALWAYS break the connection off
            //$node->removeLink($this);
            //$node->update();
        }
        elseif ($node->getId() == $this->getDestinationNode()['id'])
        {
            $this->clearDestinationNode();
            //$node->removeLink($this);
            //$node->update();
        }
        else
        {
            // Throw exception
            throw new BadFunctionCallException('passed a Node that is not connected to this Link');
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
    //</editor-fold>
//<editor-fold desc="AssociativeArray functions" defaultstate="collapsed">
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
     * diagramId String this is the ID of the parent
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
    public function loadAssociativeArray($associativeArray)
    {
        parent::loadAssociativeArray($associativeArray);
        if(isset($associativeArray['originNode']))
        {
            $this->originNode = $associativeArray['originNode'];
        }
        else
        {
            $this->originNode = NULL;
        }
        if(isset($associativeArray['destinationNode']))
        {
            $this->destinationNode = $associativeArray['destinationNode'];
        }
        else
        {
            $this->destinationNode = NULL;
        }
        
    }
    

    public function setAssociativeArray($associativeArray)
    {
    	// TODO: Make new storage function to use fewer sql queries
    	// Temporary fix for checking origin and destination node changes
    	if ($associativeArray['originNode']['id'] != $this->originNode['id'])
    	{
    		// Original node
    		$type = $this->storage->getTypeFromUUID($this->originNode['id']);
    		$originalNode = new $type($this->storage, $this->originNode['id']);
    		$originalNode->removeLink($this);
    	
    		// New node
    		$newType = $this->storage->getTypeFromUUID($associativeArray['originNode']['id']);
    		$newNode = new $newType($this->storage, $associativeArray['originNode']['id']);
    		$newNode->addLink($this);
    	
    	}
    	 
    	if ($associativeArray['destinationNode']['id'] != $this->destinationNode['id'])
    	{
    		// Original node
    		$type = $this->storage->getTypeFromUUID($this->destinationNode['id']);
    		$originalNode = new $type($this->storage, $this->destinationNode['id']);
    		$originalNode->removeLink($this);
    		 
    		// New node
    		$newType = $this->storage->getTypeFromUUID($associativeArray['destinationNode']['id']);
    		$newNode = new $newType($this->storage, $associativeArray['destinationNode']['id']);
    		$newNode->addLink($this);
    	}
    	
    	$this->loadAssociativeArray($associativeArray);
    	 
    	$this->update();
    }

    //</editor-fold>
//</editor-fold>
//<editor-fold desc="Storage Functions" defaultstate="collapsed">
    /**
     * function that will save this object to the database
     * @param WriteStorable $datastore this is the data store to write to
     */
    public function save()
    {
        // Send info required to save dataflow to the data store
        $this->storage->saveLink($this->id, $this->label, get_class($this), 
                $this->originator, $this->x, $this->y, $this->originNode['id'], 
                $this->destinationNode['id'], $this->parent);
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
