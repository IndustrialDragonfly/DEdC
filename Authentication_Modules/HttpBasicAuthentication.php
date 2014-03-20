<?php

/**
 * Uses Http Basic Authentication to verify the connecting client.
 * Unlike most classes for DEdC, this class currently can directly
 * communicate with the client in order to handle failed authentication (i.e.
 * allow multiple authentication attempts)
 *
 * @author eugene
 */
class HttpBasicAuthentication implements Authenticatable
{
    protected $storage;
    protected $id;
    protected $organization;
    protected $password;
    
    /**
     * 
     * @param type $storage
     * @param type $id
     * @param type $password
     */
    public function __construct($storage, $id, $password)
    {
        $this->storage = $storage;
        $this->id = $id;
        $this->password = $password;
    }
    
    /**
     * Verify that the given password matches the hash in the database
     * @return Boolean
     */
    public function authenticate()
    {
        // Get the user's hash from the database
        $hash = $this->storage->getHash($this->id);
        
        // Verify the password
        return password_verify($this->password, $hash);
    }
    
    /**
     * Get the salted hash of the password
     * @return String
     */
    public function getToken()
    {
        // Just a basic check for the password
        if (isset($this->password))
        {
            // Create a hash using bcrypt, cost is 10 (default)
            return password_hash($this->password, PASSWORD_DEFAULT);
        }
        else
        {
            throw new BadFunctionCallException("Password must be set to generate a hash.");
        }
    }
   
}
