<?php

/**
 * Generic class which will hold authentication information, including username,
 * organization, and credentials (of any type).
 *
 * @author Eugene Davis
 */
abstract class AuthenticationInformation
{
    /**
     *
     * @var String
     */
    private $userName;
    /**
     *
     * @var String
     */
    private $organization;
    /**
     * Holds the credential information - could be password, token, etc
     * @var Mixed
     */
    private $credentials;
    
    /**
     * Child function should check the credentials are in the right format
     * then call this constructor
     * @param String $orguser
     * @param Mixed $credentials
     * @throws BadMethodCallException
     */
    public function __construct($orguser, $credentials)
    {
         // Check that the orguser variable is a string
        if (!is_string($orguser))
        {
            // TODO: Should be a bad constructor call
            throw new BadMethodCallException("Constructor not passed a string for organization/username.");
        }
        
        // Break the orguser variable into its component parts (format is <Organization>/<Username>)
        $orgUserExplodedArray = explode("/", $orguser);
        
        // Check that this resulted in two strings in the array
        if (count($orgUserExplodedArray) != 2)
        {
            // TODO: Should be a bad constructor call
            throw new BadMethodCallException("Constructor not passed a properly formatted string for organization/user.");
        }
        
        $this->userName = $orgUserExplodedArray[0];
        $this->organization = $orgUserExplodedArray[1];
        
        // Set the credentials variable
        $this->credentials = $credentials;
    }
        
    /**
     * Returns the UserName
     * @return String
     */
    public function getUserName()
    {
        return $this->userName;
    }
    
    /**
     * Returns the organization name
     * @return String
     */
    public function getOrganization()
    {
        return $this->organization;
    }
    
    /**
     * Returns the credentials (maybe password, maybe token, etc)
     * @return Mixed
     */
    public function getCredentials()
    {
        return $this->credentials;
    }
    
    /**
     * Returns the string naming the authentication method to use
     * @return String
     */
    abstract function getAuthenticationMethod();

}
