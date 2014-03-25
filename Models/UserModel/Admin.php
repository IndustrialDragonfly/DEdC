<?php
require_once "User.php";
/**
 * The Admin class models administrative users. It inherits everything from
 * the base User class, except for overriding isAdmin to return true.
 *
 * @author Eugene Davis
 */
class Admin extends User
{
    //<editor-fold desc="Constructor" defaultstate="collapsed">
    /**
     * Pass the username and organization name to create an admin user.
     * @param String $Name
     * @param String $Org
     */
    public function __construct($Name, $Org)
    {
        parent::__construct($Name, $Org);
    }

    //</editor-fold>
    //<editor-fold desc="Accessor functions" defaultstate="collapsed">
    /**
     * Returns whether a user is admin. For an admin user, it is hardcoded to
     * return true.
     * @return boolean
     */
    public function isAdmin()
    {
        return TRUE;
    }
    //</editor-fold>
}

?>
