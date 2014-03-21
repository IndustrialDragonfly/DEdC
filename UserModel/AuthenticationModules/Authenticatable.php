<?php
/**
 * Interface for authentication modules to implement.
 * @author eugene
 */
interface Authenticatable
{
    /**
     * Verify that the given password matches the hash in the database
     * @return Boolean
     */
    public function authenticate();
    
    /**
     * Return an authentication token
     * @return String
     */
    public function getToken();
    
    /**
     * Save a new user in or update an existing one
     * May throw an exception for certain types if they must be added other ways
     * (such as LDAP)
     */
    public function saveNew();
}
