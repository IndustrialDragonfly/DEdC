<?php

/**
 * Description of DiaNode
 *
 * @author Josh Clark
 * @author Eugene Davis
 */
class DiaNode extends Node
{
    //<editor-fold desc="Attributes" defaultstate="collapsed">
    /**
     * The ID object for a subDiagram
     * @var ID
     */
    protected $subDiagram;

    //</editor-fold>
    //<editor-fold desc="Constructor" defaultstate="collapsed">

    /**
     * Constructs the DiaNode. Always requires a storage object and a user If passed an ID of
     * an existing DiaNode, loads that from the storage object. If passed the ID
     * of an existing Diagram, creates a new DiaNode in that object. If passed
     * an associativeArray which represents a DiaNode, loads that.
     * @param Readable,Writeable $storage
     * @param User $user
     * @param ID $id (Optional if associative array is passed instead)
     * @param Mixed[] $associativeArray (Optionial if ID is passed instead)
     */
    public function __construct()
    {
        if (func_num_args() == 3)
        {
            // check the second parameter is an ID
            if (is_a(func_get_arg(2), "ID"))
            {
                //the second parameter is an ID
                //check to see if the second parameter is a DiaNode
                $type = func_get_arg(0)->getTypeFromUUID(func_get_arg(2));
                if (is_subclass_of($type, "DiaNode"))
                {
                    $this->ConstructDiaNodeByID(func_get_arg(0), func_get_arg(1), func_get_arg(2));
                }
                // Otherwise pass up (if not a Diagram ID, will be handled by Node)
                else
                {
                    // Parent (Node) handles authorization
                    parent::__construct(func_get_arg(0), func_get_arg(1), func_get_arg(2));
                }
            }
            else if (is_array(func_get_arg(2)))
            {
                $this->ConstructDiaNodeFromAssocArray(func_get_arg(0), func_get_arg(1), func_get_arg(2));
            }
            else
            {
                throw new BadConstructorCallException("Invalid third parameter, is neither an ID or an assocative array");
            }
        }
        else
        {
            throw new BadConstructorCallException("Invalid number of input parameters were passed to the DiaNode Constructor");
        }
    }
    
        /**
     * "Constructs" DiaNode by loading from an ID
     * DiaNode($storage, $user, $id)
     * @param Readable,Writable $storage
     * @param User $user
     * @param ID $id
     * @throws BadConstructorCallException
     */
    protected function ConstructDiaNodeByID($storage, $user, $id)
    {
        $this->id = $id;
        $this->setStorage($storage);
        $assocativeArray = $this->storage->loadDiaNode($this->id);
        
        // TODO: Consider placing auth step in a function at a high level as it repeats a lot
        // Authorization step
        if($this->verifyThenSetUser($user, $assocativeArray['userId']))
        {
            $this->loadAssociativeArray($assocativeArray);
        }
        else
        {
            // TODO: Should throw an authorization exception
            throw new BadConstructorCallException("The user is not authorized to access this object.");
        }
        
        $this->loadAssociativeArray($assocativeArray);
        $this->save();
    }
    
     /**
     * "Constructor" for DiaNode when passed storage, a user, and an associative array
     * DiaNode($storage, $user, $associativeArray)
     * @param Readable,Writeable $storage
     * @param User $user
     * @param Mixed[] $associativeArray
     * @throws BadConstructorCallException
     */
    protected function ConstructDiaNodeFromAssocArray($storage, $user, $associativeArray)
    {
        // Verify that childDIagramId belongs to the same user (authorization)
        if(isset($associativeArray['childDiagramId']))
        {
            $id = $associativeArray['childDiagramId'];
            $type = $this->storage->getTypeFromUUID($id);
            if (is_subclass_of($type, "Diagram"))
            {
                // TODO: Adder a getUserOwningID function to Storage bridge to do this in fewer queries
                $Diagram = new $type($storage, $user, $id);

                // Check if user is authorized to access the Diagram before
                // adding this element to the diagram.
                if ($this->verifyUser($user, $Diagram->getUser()->getId()))
                {
                    $this->parent = $id;
                }
                else
                {
                    // TODO: Should throw an authorization exception
                    throw new BadConstructorCallException("The user is not authorized to perform this operation.");
                }
            }
        }
        // Third parameter was an array pass this constructor on to the parent constructor
        // handles the rest of authorization
        parent::__construct($storage, $user, $associativeArray);   
    }

    //</editor-fold>
    //<editor-fold desc="Accessor functions" defaultstate="collapsed">
    /**
     * This function returns the ID of the Diagram contained within this DiaNode
     * @return String
     */
    public function getSubDiagram()
    {
        return $this->subDiagram;
    }

    /**
     *  This function chages the ID of the Diagram contained within this DiaNode
     * @param String $aDiagramID The id of the Diagram to be placed within this DiaNode
     * @throws BadFunctionCallException if the input is not a Diagram's ID
     */
    public function setSubDiagram($aDiagramID)
    {
        $type = $this->storage->getTypeFromUUID($aDiagramID);
        if (is_subclass_of($type, "Diagram"))
        {
            $subDia = new $type($this->storage, $this->user, $aDiagramID);
            // TODO: Update verifyUser to accept two users or a user and a string
            if ($this->verifyUser($user, $subDia->getUser()->getId()))
            {
                $this->subDiagram = $aDiagramID;
                $this->update();
            }
            else
            {
                // TODO: Make authentication error
                throw new BadFunctionCallException("User not authorized for this operation.");
            }
        }
        else
        {
            throw new BadFunctionCallException("Input parameter was not a Diagram");
        }
    }

    /**
     * Returns an assocative array representing the entity object. This 
     * assocative array has the following elements and types:
     * id ID
     * label String
     * userId String
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
        $diaNodeArray = parent::getAssociativeArray();
        $diaNodeArray['childDiagramId'] = $this->subDiagram;

        return $diaNodeArray;
    }

    /**
     * Takes an assocative array representing an object and loads it into this
     * object.
     * @param Mixed[] $assocativeArray
     */
    public function loadAssociativeArray($associativeArray)
    {
        parent::loadAssociativeArray($associativeArray);

        if(isset($associativeArray['childDiagramId']))
        {
            $this->subDiagram = $associativeArray['childDiagramId'];
        }
        else
        {
            $this->subDiagram = null;
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
        // Call storage object's saveDiaNode
        $this->storage->saveDiaNode($this->subDiagram, $this->id);
    }

    /**
     * Function that deletes this object from the database
     */
    public function delete()
    {
        // Call the parent delete function AFTER child delete function
        $this->storage->deleteDiaNode($this->id);

        parent::delete();
    }

    /**
     * Refreshes the object in the storage medium, probably should later have a 
     * dedicated function in the storage medium in the future.
     */
    public function update()
    {
        // Cannot call removeAllLinks in Node delete function
        $this->storage->deleteDiaNode($this->id);
        $this->storage->deleteNode($this->id);
        $this->save();
    }

    //</editor-fold>
}
?>

