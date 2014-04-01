<?php

/**
 * This class represents an object that has a UUID, a label and can be stored 
 * into some manner of storage medium
 * 
 * Known direct subclasses:
 *    Element
 *    Diagram
 *
 * @author Josh Clark
 */
abstract class Entity
{
    //<editor-fold desc="Attributes" defaultstate="collapsed">
    /**
     * This is a container which holds the name of the object
     * @var String
     */
    protected $label;

    /**
     * This contains a universally unique identifier
     * @var ID
     */
    protected $id;

    /**
     * User that constructs the Entity
     * @var User 
     */
    protected $user;

    /**
     * The Owner that orginally created the Entity
     * @var Owner
     */
    protected $owner;

    /**
     * Storage object, should be readable and/or writable (depending on whether
     * this is a normal data store, import data source, or export data format)
     * @var DatabaseStorage
     */
    protected $storage;

    //</editor-fold>
    //<editor-fold desc="Constructor" defaultstate="collapsed">
    /**
     * This is a constructor that creates an new entity.  This takes in a 
     * variable number of parameters, the first parameter is always a storage 
     * medium, the second paramenter is always a user, the third parameter is
     * optional and contains an associative array.  If one parameter is passed 
     * the object creates and empty new object.In both cases a new ID is generated.
     * @param ReadStorable&WriteStorable $storage
     * @param User $user
     * @param Mixed[] $assocativeArray (optional)
     */
    public function __construct()
    {
        // Generate a fresh ID
        $this->id = new ID();
        $this->setStorage(func_get_arg(0));
        
        // Since this is a new object, no need for authorization, just set
        // the user as the owner
        $this->user = func_get_arg(1);
        //if there was only 2 parameters, user and storage just create an empty object
        if(func_num_args() == 2)
        {
            $this->ConstructNewEntity();
            
        }
        // If there were three parameters, and the third was an associative array
        else if (func_num_args() == 3)
        {
            $this->ConstructEntityFromAssocArray(func_get_arg(2));
        }
        else
        {
            throw new BadConstructorCallException("An incorrect number of parameters were passed to the Entity constructor.");
        }
    }

    /**
     * "Constructor" for Entity when Entity is only passed storage and user
     * Entity($storage, $user)
     */
    protected function ConstructNewEntity()
    {
        $this->label = '';
        $this->owner = new Owner($this->user->getUserName(), $this->user->getOrganization());
    }
    
    /**
     * "Constructor" for Entity when entity is passed storage, a user, and an associative array
     * Entity($storage, $user, $associativeArray)
     * @param Mixed[] $associativeArray
     * @throws BadConstructorCallException
     */
    protected function ConstructEntityFromAssocArray($associativeArray)
    {
        if (is_array($associativeArray))
            {
                $this->loadAssociativeArray($associativeArray);
                $this->owner = $associativeArray['owner'];                
            }
            else
            {
                throw new BadConstructorCallException("Third parameter was not an associative array.");
            }
    }
    
    //</editor-fold>
    //<editor-fold desc="Accessor functions" defaultstate="collapsed">
    //<editor-fold desc="label Accessors" defaultstate="collapsed">
    /**
     * This is a function that sets the label of this object
     * @param String $newLabel
     */
    public function setLabel($newLabel)
    {
        if ($this->label != $newLabel)
        {
            $this->label = $newLabel;
            $this->update();
        }
    }

    /**
     * This is a function that returns the label of the current opject
     * @return String 
     */
    public function getLabel()
    {
        return $this->label;
    }

    //</editor-fold>
    //<editor-fold desc="id Accessors" defaultstate="collapsed">
    /**
     * This function returns the UUID of this object
     * @return ID
     */
    public function getId()
    {
        return $this->id;
    }

    //intentionally no setId()
    //</editor-fold>
    //<editor-fold desc="owner Accessors" defaultstate="collapsed">
    /**
     * This is a function that sets the Originator of this object
     * @param User $newUser
     */
    protected function setUser($newUser)
    {
        if (is_a($newUser, "User"))
        {
            $this->user = $newUser;
        }
        else
        {
            throw new BadConstructorCallException("Passed user object is not/does not inherit user.");
        }
    }
    
    /**
     * Verifies that the user passed has the same ID as the user ID for 
     * the object stored in the database.
     * If it fails, throws an exception.
     * @param User $user
     * @param String|User $storedUser
     */
    protected function verifyThenSetUser($user)
    {
        // Ensure it is a user object
        if (is_a($user, "User"))
        {
            // Compare $user's ID to the user stored in the database for the object
            if ($this->owner->authorize($user))
            {
                $this->setUser($user);
            }
            else
            {
                throw new BadFunctionCallException("Passed user object does not own object to modify.");
            }
        }
        else
        {
            // TODO: Authorization exception
            throw new BadFunctionCallException("Passed user object is not/does not inherit user.");
        }
    }

    /**
     * This is a function that retrieves the userId of this object
     * @return String
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * This function returns the Owner of the Entity
     * @return Owner
     */
    public function getOwner()
    {
    	return $this->owner;
    }

    //</editor-fold>
    
    //<editor-fold desc="Storage Accessors" defaultstate="collapsed">
    /**
     * This is a function that retrieves the Storage object that is associated 
     * with this object
     * @return Readable&Writable
     */
    public function getStorage()
    {
        return $this->storage;
    }
    
    /**
     * Sets the storage object, and checks that it is of the right types.
     * @param Readable&Writable $storage
     */
    protected function setStorage($storage)
    {
        
        if (!is_subclass_of($storage, "ReadStorable"))
        {
            throw new BadConstructorCallException("Passed storage object does not implement ReadStorable.");
        }
        if (!is_subclass_of($storage, "WriteStorable"))
        {
            throw new BadConstructorCallException("Passed storage object does not implement WriteStorable.");
        }
        $this->storage = $storage;
    }

    //</editor-fold>
    //</editor-fold>
    //<editor-fold desc="AssociativeArray functions" defaultstate="collapsed">
    /**
     * Returns an assocative array representing the entity object. This 
     * assocative array has the following elements and types:
     * 
     * id String
     * label String
     * user Mixed[]
     *      userName String
     *      organization String
     * organization String
     * type String
     * genericType String
     *  
     * @returns Mixed[]
     */
    public function getAssociativeArray()
    {
        $entityArray = array();

        $entityArray['id'] = $this->id;
        $entityArray['label'] = $this->label;
        $entityArray['owner'] = $this->owner->getAssociativeArray();
        $entityArray['type'] = get_class($this);

        $genericType = NULL;

        // Figure out the generic type - i.e. Link, Node, diaNode or Diagram
        if (is_subclass_of($this, "Diagram"))
        {
            $genericType = "Diagram";
        }
        elseif (is_subclass_of($this, "DiaNode"))
        {
            $genericType = "diaNode";
        }
        elseif (is_subclass_of($this, "Node"))
        {
            $genericType = "Node";
        }
        elseif (is_subclass_of($this, "Link"))
        {
            $genericType = "Link";
        }
        else
        {
            throw new BadFunctionCallException("This object does not decend from an valid base classes, it should decend from either: Diagram, DiaNode, Node or Link");
        }

        $entityArray['genericType'] = $genericType;

        return $entityArray;
    }

    /**
     * Takes an assocative array representing an object and loads it into this
     * object. Loads the label, and the organization;
     * @param Mixed[] $assocativeArray
     */
    public function loadAssociativeArray($associativeArray)
    {
        if(isset($associativeArray['label']))
        {
            $this->label = $associativeArray['label'];
        }
        else
        {
            $this->label = "";
        }
    }
    
    /**
     * Generic call to allow an element to be externally loaded by an assoc array
     * @param Mixed[] $associativeArray
     */
    public function setAssociativeArray($associativeArray)
    {
    	$this->loadAssociativeArray($associativeArray);
    	$this->update();
    }

    //</editor-fold>
}
?>
