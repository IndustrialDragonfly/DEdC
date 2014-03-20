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
}
