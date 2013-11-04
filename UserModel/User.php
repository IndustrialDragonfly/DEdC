<?php

/**
 * The User class models regular users, without admin rights.
 *
 * @author Eugene Davis
 */
class User
{

    //<editor-fold desc="Attributes" defaultstate="collapsed">
    private $Name;
    private $ID;
    private $Organization;

    //</editor-fold>
    //<editor-fold desc="Constructor" defaultstate="collapsed">
    /**
     * Pass the contructor the username and organization name to create
     * a regular User object.
     * @param String $Name
     * @param String $Org
     */
    public function __construct($Name, $Org)
    {
        $this->Name = $Name;
        $this->Organization = $Org;
        // Generate random ID
        $this->ID = $this->generateId();
    }

    //</editor-fold>
    //<editor-fold desc="Accessor functions" defaultstate="collapsed">
    /**
     * Sets the name of the user
     * @param String $newName
     */
    public function setName($newName)
    {
        $this->Name = $newName;
    }

    /**
     * Returns the user name
     * @return String
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * Returns the randomly generated ID string
     * @return String
     */
    public function getId()
    {
        return $this->ID;
    }

    /**
     * Sets a new organization for the user
     * @param String $newOrg
     */
    public function setOrganization($newOrg)
    {
        $this->Organization = $newOrg;
    }

    /**
     * Returns the string of the organization name
     * @return String
     */
    public function getOrganization()
    {
        return $this->Organization;
    }

    /**
     * Returns whether the user is an admin. For a regular user, is hardcoded
     * to return false.
     * @return boolean
     */
    public function isAdmin()
    {
        return FALSE;
    }

//</editor-fold>
    /**
     * Generates an ID of a given bit size using the OpenSSL PRNG.
     * @return String
     */
    private function generateId()
    {
        $length = 256;
        $numberOfBytes = $length / 8;
        return strtr(base64_encode(openssl_random_pseudo_bytes($numberOfBytes)), "+/=", "xxx");
    }

}

?>
