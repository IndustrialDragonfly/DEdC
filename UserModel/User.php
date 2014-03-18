<?php

/**
 * The User class models regular users, without admin rights.
 *
 * @author Eugene Davis
 */
class User
{

    //<editor-fold desc="Attributes" defaultstate="collapsed">
    private $userName;
    private $id;
    private $organization;
    private $hash;

    //</editor-fold>
    //<editor-fold desc="Constructor" defaultstate="collapsed">
    /**
     * Pass the contructor the username and organization name to create
     * a regular User object.
     * @param String $useruserName
     * @param String $Org
     * @throws InvalidArgumentException if empty string passed for name or organization
     */
    public function __construct($useruserName, $Org)
    {
        if ($useruserName == "" || $Org == "")
        {
            throw new InvalidArgumentException("Constructor requires a value for name and organization");
        }
        $this->userName = $useruserName;
        $this->organization = $Org;
        // Generate random id
        $this->id = $this->generateId();
    }

    //</editor-fold>
    //<editor-fold desc="Accessor functions" defaultstate="collapsed">
    /**
     * Sets the name of the user
     * @param String $newuserName
     * @throw InvalidArgumentException thrown when newuserName is an empty string
     */
    public function setuserName($newuserName)
    {
        if ($newuserName == "")
        {
            throw new InvalidArgumentException("setuserName requires a value for name");
        } else
        {
            $this->userName = $newuserName;
        }
    }

    /**
     * Returns the user name
     * @return String
     */
    public function getuserName()
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
    public function setorganization($newOrg)
    {
        if ($newOrg == "")
        {
            throw new InvalidArgumentException("setorganization requires a value for organization");
        } else
        {
            $this->organization = $newOrg;
        }
    }

    /**
     * Returns the string of the organization name
     * @return String
     */
    public function getorganization()
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
}

?>
