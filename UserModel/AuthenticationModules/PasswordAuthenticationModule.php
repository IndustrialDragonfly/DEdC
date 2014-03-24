<?php

/**
 * Uses a password to verify the connecting client.
 *
 * @author Eugene Davis
 */
class PasswordAuthenticationModule implements Authenticatable
{
    protected $storage;
    protected $id;
    protected $organization;
    protected $password;
    
    /**
     * 
     * @param type $storage
     * @param type $id
     * @param type $authenticationInfo (AuthenticationInformation object)
     */
    public function __construct($storage, $id, $authenticationInfo)
    {
        $this->storage = $storage;
        $this->id = $id;
        $this->password = $authenticationInfo->getCredentials();
    }
    
    /**
     * Verify that the given password matches the hash in the database
     * @return Boolean
     */
    public function authenticate()
    {
        // Get the user's hash from the database
        $hash = $this->storage->getHash($this->id->getId());
        
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
   
    public function saveNew()
    {
        $this->storage->saveHash($this->id, $this->getToken());
    }
}
