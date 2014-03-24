<?php
require_once "AuthenticationInformation.php";

/**
 * PasswordAuthentication extends AuthenticationInformation to handle credentials
 * that use just a password.
 *
 * @author Eugene Davis
 */
class PasswordAuthentication extends AuthenticationInformation
{
    /**
     * Checks that the credentials are a string (since this is for passwords)
     * then calls the parent constructor to do the rest.
     * @param String $orguser
     * @param String $credentials
     * @throws BadMethodCallException
     */
    public function __construct($orguser, $credentials)
    {
        // Check that the credentials is a string (a password)
        if (!is_string($credentials))
        {
            // TODO: Should be a bad constructor call
            throw new BadMethodCallException("Constructor not passed a string for password.");
        }
        parent::__construct($orguser, $credentials);
    }
    
    /**
     * Returns "PasswordAuthentication" as the authentication type
     * @return String
     */
    public function getAuthenticationMethod()
    {
        return "PasswordAuthentication";
    }
}
