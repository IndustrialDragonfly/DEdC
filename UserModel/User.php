<?php

/**
 * The User class models regular users, without admin rights.
 *
 * @author Eugene Davis
 */
class User
{

    //<editor-fold desc="Attributes" defaultstate="collapsed">
    private $id;
    private $userName;
    private $organization;
    private $hash;

    //</editor-fold>
    //<editor-fold desc="Constructor" defaultstate="collapsed">
    
    /**
     * Create a new User object with userName and organization
     * @param String $id (optional)
     * @param String $userName
     * @param String $organization
     * @param String $password Password if no id is given, otherwise this is the hash
     * @param {ReadStorable,WriteStorable} $datastore 
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        $this->storage = func_get_arg(0);
        // New User, will get generated id
        if (func_num_args() == 4)
        {
            // Given userName, organization, and password
            $this->id = $this->generateId();
            $this->userName = func_get_arg(1);
            $this->organization = func_get_arg(2);
            $this->setPassword(func_get_arg(3));
        }
        // Load user from id
        else if (func_num_arg() == 2)
        {
            // Given id
            $associativeArray = $this->load(func_get_arg(1));
            $this->loadAssociativeArray($associativeArray);
        }
        else
        {
            throw new InvalidArgumentException("constuctor requires either an id, userName, organization, and hash or userName, organization, and hash.");
        }
    }

    //</editor-fold>
    //<editor-fold desc="Accessor functions" defaultstate="collapsed">
    /**
     * Sets the name of the user
     * @param String $newuserName
     * @throw InvalidArgumentException thrown when newuserName is an empty string
     */
    public function setUserName($newuserName)
    {
        if ($newuserName == "")
        {
            throw new InvalidArgumentException("setUserName requires a value for name");
        } else
        {
            $this->userName = $newuserName;
        }
    }

    /**
     * Returns the user name
     * @return String
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Returns the randomly generated id string
     * @return String
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets a new organization for the user
     * @param String $newOrg
     * @throws InvalidArgumentException when newOrg is empty string
     */
    public function setOrganization($newOrg)
    {
        if ($newOrg == "")
        {
            throw new InvalidArgumentException("setOrganization requires a value for organization");
        } else
        {
            $this->organization = $newOrg;
        }
    }

    /**
     * Returns the string of the organization name
     * @return String
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Returns whether the user is an admin. For a regular user, is hardcoded
     * to return false.
     * @return boolean
     */
    public function isAdmin()
    {
        return FALSE;
    }

//</editor-fold>
    /**
     * This is a function that generates a UUid String with a length of 265 bits
     * @return String
     */
    private function generateId()
    {
        $length = 256;
        $numberOfBytes = $length / 8;
        // Replaces all instances of +, = or / in the Base64 string with x
        return str_replace(array("+", "=", "/"), array("x", "x", "x"), base64_encode(openssl_random_pseudo_bytes($numberOfBytes)));
    }
    
    /**
     * Hashes password and sets it
     * @param String $password
     */
    public function setPassword($password)
    {
        if (!is_string($password))
        {
            throw new InvalidArgumentException("setPassword requires a String for password.");
        }
        // Create a hash using bcrypt, cost is 10 (default)
        $this->hash = password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Verify that $password's hash matches the stored hash
     * @param String $password
     * @return boolean
     */
    public function authenticate($password)
    {
        if (!is_string($password))
        {
            throw new InvalidArgumentException("authenticate requires a String for password.");
        }
        
        // Verify the password
        return password_verify($password, $this->hash);
    }
    
    /**
     * Takes an assocative array representing an object and loads it into this
     * object.
     * @param Mixed[] $assocativeArray
     */
    protected function loadAssociativeArray($associativeArray)
    {
        if (!is_array($associativeArray))
        {
            throw new InvalidArgumentException("loadAssociativeArray was called without an associative array");
        }
        
        // TODO: Check types
        $this->id = $associativeArray["id"];
        $this->userName = $associativeArray["userName"];
        $this->organization = $associativeArray["organization"];
        $this->hash = $associativeArray["hash"];
    }
    
    /**
     * Save the User to the database
     */
    public function save()
    {
        $this->storage->saveUser($this->getId(), $this->getUserName(), $this->getOrganization(), $this->getHash(), $this->isAdmin());
    }
    
    /**
     * Load the associative array from the database. If id is not set, userName 
     * and organization will be used, else the id is used.
     * @return type
     */
    public function load()
    {
        if ($this->id == NULL)
        {
            return $this->storage->loadUser($this->userName, $this->organization);
        }
        else
        {
            return $this->storage->loadUser($this->id);
        } 
    }
}

?>
