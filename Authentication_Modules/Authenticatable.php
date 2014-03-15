<?php
/**
 * Interface for authentication modules to implement.
 * @author eugene
 */
interface Authenticatable
{
    /**
     * Authenticates the client, and returns true if the client successfully 
     * authenticated, or false otherwise.
     * @return Bool
     */
    public function authenticateClient();
}
