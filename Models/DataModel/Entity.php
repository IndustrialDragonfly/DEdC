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
     * UUID of the originator of this DFD
     * @var ID 
     */
    protected $user;

    /**
     * This is a container for the organization that this object belongs to
     * @var String
     */
    protected $organization;

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
     * @param {ReadStorable,WriteStorable} $datastore
     * @param User $user
     * @param Mixed[] $assocativeArray 
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
            $this->ConstructEntityFromAssocArray(func_get_arg(1));
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
        $this->organization = '';
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
                $this->loadAssociativeArray(func_get_arg(1));
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
     * @return String
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
     * @param String $newOriginator
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
        $this->update();
    }
    
    /**
     * Verifies that the user passed has the same ID as the user ID for 
     * the object stored in the database.
     * If it fails, throws an exception.
     * @param User $user
     * @param String $storedUser
     */
    protected function verifyThenSetUser($user, $storedUser)
    {
        // Ensure it is a user object
        if (is_a($user, "User"))
        {
            // Compare $user's ID to the user stored in the database for the object
            if ($this->verifyUser($user, $storedUser))
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
     * Checks if the user passed in matches the string passed in (from Storage)
     * @param User $user
     * @param String $storedUser
     * @returns bool
     */
    protected function verifyUser($user, $storedUser)
    {
        // If user matches the string, return true
        if ($user->getId() == $storedUser)
            {
                return true;
            }
        // Otherwise return false
        return false;
    }

    /**
     * This is a function that retrieves the Originator of this object
     * @return String
     */
    public function getUser()
    {
        return $this->user;
    }

    //</editor-fold>
    //<editor-fold desc="Organization Accessors" defaultstate="collapsed">
    /**
     * This is a function that sets the Organization that this object belongs to
     * @param String $newOrg
     */
    public function setOrganization($newOrg)
    {
        if ($this->organization != $newOrg)
        {
            $this->organization = $newOrg;
            $this->update();
        }
    }

    /**
     * This is a function that retrieves the Organization that this object 
     * belongs to
     * @return String
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    //</editor-fold>
    //<editor-fold desc="Storage Accessors" defaultstate="collapsed">
    /**
     * This a a function that will set the storage object that is associated 
     * with this object
     * 
     * should this function exist?
     *   -nope
     * 
     * @param Readable/Writable $newStorage
     */
    /* disabled
      public function setStorage($newStorage)
      {
      $this->storage = $newStorage;
      } */

    /**
     * This is a function that retrieves the Storage object that is associated 
     * with this object
     * @return Readable/Writable
     */
    public function getStorage()
    {
        return $this->storage;
    }
    
    /**
     * Sets the storage object, and checks that it is of the right types.
     * @param Readable Writable $storage
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
        $this->storage = func_get_arg($storage);
    }

    //</editor-fold>
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
     *  
     * @returns Mixed[]
     */
    public function getAssociativeArray()
    {
        $entityArray = array();

        $entityArray['id'] = $this->id;
        $entityArray['label'] = $this->label;
        $entityArray['originator'] = $this->user;
        $entityArray['organization'] = $this->organization;
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
     * object.
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
        
        // No longer possible to load without having a user in the first place.
        /*if(isset($associativeArray['originator']))
        {
            $this->user = $associativeArray['originator'];
        }
        else
        {
            $this->user = "";
        }*/
        // TODO: Handle organization by grabbing it from user object
        if(isset($associativeArray['organization']))
        {
            $this->organization = $associativeArray['organization'];
        }
        else
        {
            $this->organization = "";
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
