<?php

/**
 * Interface for AuthenticationHandlers that takes information from the client 
 * and converts it to an AuthenticationInformation object.
 * @author Jacob Swanson/Eugene Davis
 */
interface AuthenticationHandleable 
{
    /**
     * Constructs an Object based on the URL
     * @param String $queryString
     */
    public function __construct($queryString);
    
    /**
     * Returns an AuthenticationInformation Object
     * @return AuthenticationInformation
     */
    public function getAuthenticationInfo();
}
