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
     * This is the constructor for the Link Class.  It takes in 3 parameters; 
     * the first is always a valid storage medium, the second one is a user 
     * the third an ID or an associative array.  If the third parameter is an id of a valid 
     * Link subclass the Link is loaded in from the storage medium.  If the 
     * third parameter was an ID of anything else it is passed to the 
     * constructor for Element for it to handle.  If the third parameter is an 
     * associative array it is likewise passed to the Element constructor for it
     *  to handle.  
     * @param {ReadStorable,WriteStorable} $datastore
     * @param User $user
     * @param ID $id    the UUID of either the parent Diagram or the id of the 
     *                      Link to be loaded 
     * @param Mixed[] $assocativeArray
     */
    public function __construct()
    {
        //check number of paramenters
        if(func_num_args() == 3)
        {
            // Check if third parameter is a ID object
            if (is_a(func_get_arg(2), "ID"))
            {
                // If ID is of a Link type object, load from the ID
                $type = func_get_arg(0)->getTypeFromUUID(func_get_arg(2));
                if (is_subclass_of($type, "Link"))
                {
                    $this->ConstructLinkByID(func_get_arg(0), func_get_arg(1), func_get_arg(2))
                }
                // If ID is of a Diagram type object, create a new Link in the Diagram
                else if (is_subclass_of($type, "Diagram"))
                {
                    $this->ConstructLinkWithDiagram(func_get_arg(0), func_get_arg(1), func_get_arg(2));
                }
                else
                {
                    throw new BadConstructorCallException("The id passed to the Link Constructor was neither a valid Link or Diagram descended object");
                }
            }
            // Third parameter should be an associative array so pass it along to Element's constructor
            else
            {
                $this->ConstructLinkFromAssocArray(func_get_arg(0), func_get_arg(1), func_get_arg(2));		
            }
        }
        else
        {
            throw new BadConstructorCallException("An incorrect number of parameters were passed to the Link constructor");
        }
    }
    
    /**
     * "Constructor" for Link objects constructed with an ID for a link
     * Link($storage, $user, $id)
     * @param Readable,Writable $storage
     * @param User $user
     * @param ID $id
     * @throws BadConstructorCallException
     */
    protected function ConstructLinkByID($storage, $user, $id)
    {
        // Never calling parent, so must handle setting up the storage
        $this->storage = $storage;
        if (!is_subclass_of($this->storage, "ReadStorable"))
        {
            throw new BadConstructorCallException("Passed storage object does not implement ReadStorable.");
        }
        if (!is_subclass_of($this->storage, "WriteStorable"))
        {
            throw new BadConstructorCallException("Passed storage object does not implement WriteStorable.");
        }
        $assocativeArray = $this->storage->loadLink($id);
        
        // Authorization step
        if($this->verifyThenSetUser($user, $assocativeArray['originator']))
        {
            $this->loadAssociativeArray($assocativeArray);
        }
        else
        {
            // TODO: Should throw an authorization exception
            throw new BadConstructorCallException("The user is not authorized to add access this object.");
        }  
        $this->id = $id;
        $this->save();
    }
    
    /**
     * "Constructor" for Link objects constructed with an ID for a diagram
     * Link($storage, $user, $id)
     * @param type $storage
     * @param type $user
     * @param type $id
     */
    protected function ConstructLinkWithDiagram($storage, $user, $id)
    {
        // Call the parent constructor and set the nodes to be empty
        // Parent (Element) handles authorization
        parent::__construct($storage, $user, $id);
        $this->originNode = NULL;
        $this->destinationNode = NULL;
        $this->save();
    }
    
    protected function ConstructLinkFromAssocArray($storage, $user, $associativeArray)
    {
        // TODO: Notice this does an __unsafe__ loadAssociativeArray call (for authorization)
        // with respect to the nodes. Though this should be rectified here by the
        // construction of the nodes, it would be good to add a setAssociative function
        // which verfies them before loading
        parent::__construct($storage, $user, $associativeArray);
        // Grab "official" associative array
        $associativeArray = $this->getAssociativeArray();
        // TODO: Merge these functions into setNode functions (if possible)
        if ($associativeArray ['originNode'] != NULL) 
        {
                // New node
                $newType = $this->storage->getTypeFromUUID($associativeArray['originNode']['id'] );
                // Provides implicit authorization since constructor fails if user is wrong
                $newNode = new $newType ( $this->storage, $this->user, $associativeArray['originNode']['id'] );
                $newNode->addLink ( $this );
        }

        if ($associativeArray ['destinationNode'] != NULL) 
        {
                // New node
                $newType = $this->storage->getTypeFromUUID( $associativeArray['destinationNode']['id'] );
                // Provides implicit authorization since constructor fails if user is wrong
                $newNode = new $newType ( $this->storage, $this->user, $associativeArray ['destinationNode'] ['id'] );
                $newNode->addLink ( $this );
        }

        $this->save ();	
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
        if (is_subclass_of($aNode, "Node"))
        {
            //if origin has not been set yet
            if ($this->originNode == NULL)
            {
                //set the origin node and add this DataFlow to its list of Links
                $this->originNode['id'] = $aNode;
                $this->originNode['label'] = $aNode->getLabel();
                
                $aNode->addLink($this);
            }
            //if the origin node has already been set
            else
            {
                //remove this DataFlow from the old origin nodes list of links and 
                //thenset the origin node to the new node and add this DataFlow to 
                //its list of Links
                $type = $this->storage->getTypeFromUUID($this->originNode->getId);
                // Provides implicit authorization since constructor fails if user is wrong
                $node = new $type($this->storage, $this->user, $this->originNode);
                $node->removeLink($this);

                $this->originNode['id'] = $aNode;
                $this->originNode['label'] = $aNode->getLabel();
                $aNode->addLink($this);
            }
            $this->update();
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
            // Provides implicit authorization since constructor fails if user is wrong
            $node = new $type($this->storage, $this->user, $this->originNode['id']);
            $node->removeLink($this);
            $this->originNode = NULL;
            //$node->update();remove link already did this
            $this->update();
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
                $this->destinationNode['id'] = $aNode;
                $this->destinationNode['label'] = $aNode->getLabel();
                
                $aNode->addLink($this);
            }
            //if the destination node has already been set
            else
            {
                //remove this DataFlow from the old destination nodes list of links and 
                //then set the destination node to the new node and add this DataFlow to 
                //its list of Links
                $type = $this->storage->getTypeFromUUID($this->destinationNode);
                $node = new $type($this->storage, $this->user, $this->destinationNode);
                $node->removeLink($this);

                $this->destinationNode['id'] = $aNode;
                $this->destinationNode['label'] = $aNode->getLabel();
                $aNode->addLink($this);
            }
            $this->update();
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
            // Provides implicit authorization since constructor fails if user is wrong
            $node = new $type($this->storage, $this->user, $this->destinationNode['id']);
            $node->removeLink($this);
            //$node->update(); remove link already did this
            $this->destinationNode = NULL;
            $this->update();
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
        if ($node == $this->getOriginNode()['id'])
        {
            $this->clearOriginNode();
            // Actually call back the node that just called and remove the node
            // since Links ALWAYS break the connection off
            //$node->removeLink($this);
            //$node->update();
        }
        elseif ($node == $this->getDestinationNode()['id'])
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
        // TODO: Merge these functions into setNode functions (if possible)
        // TODO: Wrap both ifs w/ a NULL and is_set check and handle appropriately
    	// TODO: Make new storage function to use fewer sql queries
    	// Temporary fix for checking origin and destination node changes
    	if ($associativeArray['originNode']['id'] != $this->originNode['id'])
    	{
            // Original node
            // Check if NULL
            if ($this->originNode != NULL)
            {
                $type = $this->storage->getTypeFromUUID($this->originNode['id']);
                $originalNode = new $type($this->storage, $this->originNode['id'], $this->user);
                $originalNode->removeLink($this);
            }

            // New node
            $newType = $this->storage->getTypeFromUUID($associativeArray['originNode']['id']);
            // Construction of the node provides authorization, as it will fail if the user doesn't match
            $newNode = new $newType($this->storage, $this->user, $associativeArray['originNode']['id']);
            $newNode->addLink($this);
    	
    	}
    	 
    	if ($associativeArray['destinationNode']['id'] != $this->destinationNode['id'])
    	{
            // Original node
            if ($this->destinationNode != NULL)
            {
                $type = $this->storage->getTypeFromUUID($this->destinationNode['id']);
                // Construction of the node provides authorization, as it will fail if the user doesn't match
                $originalNode = new $type($this->storage, $this->user, $this->destinationNode['id']);
                $originalNode->removeLink($this);
            }

            // New node
            $newType = $this->storage->getTypeFromUUID($associativeArray['destinationNode']['id']);
            $newNode = new $newType($this->storage, $this->user, $associativeArray['destinationNode']['id']);
            $newNode->addLink($this);
    	}
    	
    	parent::setAssociativeArray($associativeArray);
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
                $this->user, $this->x, $this->y, $this->originNode['id'], 
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
