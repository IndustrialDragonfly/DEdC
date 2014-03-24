<?php

/**
 * Interface for AuthenticationHandlers that takes information from the client 
 * and converts it to an AuthenticationInformation object.
 * @author Jacob Swanson/Eugene Davis
 */
interface AuthenticationHandleable 
{
    /**
     * Constructs an Object based on the URL's parsed query string
     * @param Mixed[] $queryArray
     */
    public function __construct($queryArray);
    
    /**
     * Returns an AuthenticationInformation Object
     * @return AuthenticationInformation
     */
    public function getAuthenticationInfo();
}
