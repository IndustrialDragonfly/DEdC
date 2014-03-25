<?php

/**
 * The User class models regular users, without admin rights.
 *
 * @author Eugene Davis/Jacob Swanson
 */
class User
{

    //<editor-fold desc="Attributes" defaultstate="collapsed">
    private $id;
    private $userName;
    private $organization;
   
    private $authModule;

    //</editor-fold>
    //<editor-fold desc="Constructor" defaultstate="collapsed">
    
    /**
     * Create a new User object
     * Create an existing User.
     * @param {ReadStorable,WriteStorable} $datastore
     * @param AuthenticationInformation $authInfo
     * 
     * Or Admin instantiation of an existing User. This will not authenticate 
     * the User object.
     * @param {ReadStorable,WriteStorable} $datastore
     * @param Admin $admin
     * @param ID $id
     * 
     * Or Admin creation of a User.
     * @param {ReadStorable,WriteStorable} $datastore
     * @param Admin $admin
     * @param AuthenticationInformation $authInfo
     * 
     * @throws BadConstructorCallException
     */
    public function __construct()
    {
        // Checking storage object
        $this->storage = func_get_arg(0);
        if (!is_subclass_of($this->storage, "ReadStorable"))
        {
            throw new BadConstructorCallException("Passed storage object does not implement ReadStorable.");
        }
        if (!is_subclass_of($this->storage, "WriteStorable"))
        {
            throw new BadConstructorCallException("Passed storage object does not implement WriteStorable.");
        }
        
        // Existing User
        // Args: storage, Auth Info (Object)
        if (func_num_args() == 2 && is_subclass_of(func_get_arg(1), "AuthenticationInformation"))
        {
            $authInfo = func_get_arg(1);
            // Get username and org from the AuthenticationInformation Object
            $this->userName = $authInfo->getUserName();
            $this->organization = $authInfo->getOrganization();
            
            // Load id
            $this->id = new ID($this->storage->getUserId($this->userName, $this->organization));
            
            // Create the authentication module
            $authMethod = $authInfo->getAuthenticationMethod();
            $this->authModule = new $authMethod($this->storage, $this->id, $authInfo);
            if (!$this->authModule->authenticate())
            {
                // TODO: Make an authentication exception
                throw new BadConstructorCallException("User failed to authenticate.");
            }
            
        }
        // Check to make sure given User is Admin
        else if (func_num_args() == 3 && is_a(func_get_arg(1), "Admin"))
        {
            // Admin instantiation of any User
            // Args: storage, Admin (User Object), ID (Object)
            if (is_a(func_get_arg(2), "ID"))
            {
                // Set User id, userName, and organization
                $this->id = new ID(func_get_arg(2));
                $assocArray = $this->storage->loadUser($this->id->getId());
                $this->userName = $assocArray["userName"];
                $this->organization = $assocArray["organization"];
                
                // There is no authentication module in this case
            }
            // New User creation
            // Args: storage, Admin (User Object), Auth Info (Object)
            else if (is_subclass_of(func_get_arg(2), "AuthenticationInformation"))
            {
                $authInfo = func_get_arg(2);
                // Generate a new id
                $this->id = new ID();
                
                // Handle username, and organization
                $this->userName = $authInfo->getUserName();
                $this->organization = $authInfo->getOrganization();
                
                // Handle the password
                $authMethod = $authInfo->getAuthenticationMethod();
                $this->authModule = new $authMethod($this->storage, $this->id, $authInfo);
            }
            else
            {
                throw new BadConstructorCallException("Third argument was not an ID nor AuthenticationInformation.");
            }
        }
        else
        {
            throw new BadConstructorCallException("Second argument was not an Admin User.");
        }
    }
    
    //</editor-fold>
    //<editor-fold desc="Accessor functions" defaultstate="collapsed">

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
     * @return ID
     */
    public function getId()
    {
        return $this->id;
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
     * Save the User to the database
     */
    public function save()
    {
        $this->storage->saveUser($this->id->getId(), $this->userName, $this->organization, $this->isAdmin());
        $this->authModule->saveNew();
    }
}

?>
