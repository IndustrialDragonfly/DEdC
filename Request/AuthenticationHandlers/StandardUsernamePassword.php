<?php

/**
 * Handles URL encoded Organization/Username and password.
 *
 * @author Jacob Swanson/Eugene Davis
 */
class StandardUsernamePassword implements AuthenticationHandleable
{
    /**
     * String in the form of ORG/USERNAME
     * @var String 
     */
    protected $orgUser;
    
    /**
     * Password
     * @var String 
     */
    protected $password;
    
    /**
     * PasswordAuthentication object
     * @var AuthenticationInformation 
     */
    protected $authInfo;
    
    /**
     * Retrieve ORG/USER, and construct an AuthenticationInformation object.
     * @param String $queryString
     */
    public function __construct($queryString) 
    {
        // Convert queryString to an associative array
        $assocArray = parse_str($queryString);
        
        // Set the organization and username
        $this->orgUser = $assocArray['orgUser'];
        
        // Set the password
        $this->password = $assocArray['password'];
        
        // Create the AuthenticationInformation object
        $this->authInfo = new PasswordAuthentication($this->orgUser, $this->password);
    }
    
    /**
     * Return the AuthenticationInformation object.
     * @return String
     */
    public function getAuthenticationInfo()
    {
        return $this->authInfo;
    }
}
