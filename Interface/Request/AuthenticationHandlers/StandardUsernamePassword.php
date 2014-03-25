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
     * @param Mixed[] $queryArray
     */
    public function __construct($queryArray) 
    {        
        // Set the organization and username
        $this->orgUser = $queryArray['orgUser'];
        
        // Set the password
        $this->password = $queryArray['password'];
        
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
