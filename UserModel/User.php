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
    
    private $authModule;

    //</editor-fold>
    //<editor-fold desc="Constructor" defaultstate="collapsed">
    
    /**
     * Create a new User object with userName and organization
     * @param {ReadStorable,WriteStorable} $datastore 
     * @param String $userName
     * @param String $organization
     * @param String $password Password if no id is given, otherwise this is the hash
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
        // Load user from id or associative array
        else if (func_num_args() == 2)
        {
            // Given id
            if (is_string(func_get_arg(1)))
            {
                $this->id = func_get_arg(1);
                $associativeArray = $this->load();
                $this->loadAssociativeArray($associativeArray);
            }
            // Given array
            elseif (is_array(func_get_arg(1)))
            {
                // TODO: Check array's values
                $this->loadAssociativeArray(func_get_arg(1));
                $this->id = $this->generateId();
            }
            else
            {
                // TODO: Throw BadConstructorCallException
                throw new BadFunctionCallException("User consturctor requires either a string or an associative array");
            }
        }
        // Given userName and organization
        else if (func_num_args() == 3)
        {
            $this->userName = func_get_arg(1);
            $this->organization = func_get_arg(2);
            
            if (!is_string($this->userName) || !is_string($this->organization))
            {
                throw new InvalidArgumentException("User constructor requires userName and organization to be Strings.");
            }
            
            $associativeArray = $this->load();

            $this->loadAssociativeArray($associativeArray);
            $this->id = $associativeArray["id"];
        }
        else
        {
            throw new InvalidArgumentException("User constuctor requires either an id, userName, organization, and hash or userName, organization, and hash.");
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
        // Get the hash from the auth module
        $this->hash = $this->authModule->getToken();
    }
    
    /**
     * Verify that $password's hash matches the stored hash
     * @return boolean
     */
    public function authenticate()
    {
        $this->authModule->authenticate();
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
        $this->userName = $associativeArray["userName"];
        $this->organization = $associativeArray["organization"];
    }
    
    /**
     * Save the User to the database
     */
    public function save()
    {
        $this->storage->saveUser($this->id, $this->userName, $this->organization, $this->hash, $this->isAdmin());
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
