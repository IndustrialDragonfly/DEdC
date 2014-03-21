<?php
/**
 * The ReadStorable interface defines the functions that a read storage class
 * must implement for use with DEdC. Its counterpart is the WriteStorable
 * class, which will define the functions required for a write storage class.
 * It is not required that both interfaces be implemented, allowing for the 
 * creation of write or read only media, such as for exporting to formats
 * like PDF
 * 
 * @author eugene
 */
interface ReadStorable
{
    //<editor-fold desc="DFD Model" defaultstate="collapsed">
    /**
     * Returns the type of element from the data store based on the given resource
     * UUID
     * 
     * @param string $resource
     */
    public function getTypeFromUUID($resource);
    
    /**
     * Returns a list of of a given type by the given type from the datastore
     * 
     * @param String $type
     */
    public function getListByType($type);
    
    /**
     * Returns a list of types from the datastore
     * 
     * @return String[]
     */
    public function getTypes();
    
     /**
     * loadNode takes as input a UUID and returns an associative array
     * of all information related to that ID from the database.
     * @param String $id
     * @returns associative array
     */
    public function loadNode($id);
    
    /**
     * loadLink takes an input of a UUID and returns an associative array of
     * all the information related to that ID from the database.
     * 
     * @param string $id
     */
    public function loadLink($id);
    
    /**
     * loadDiagram takes an input of a UUID and returns an associative array of all
     * the information related to that ID from the database.
     * @param String $id
     */
    public function loadDiagram($id);
    
    /**
     * For a given id that is of type diaNode, return what dfd it maps to
     * @param String $id
     * @return String
     */
    public function loadDiaNode($id);
    //</editor-fold>
    
    //<editor-fold desc="User Model" defaultstate="collapsed">

    /**
     * Get a User's id String
     * @param String userName
     * @param String organization
     * @return String
     */
    public function getUserId($userName, $organization);

    
    /**
     * Load a User using the id
     * @param String id
     * @return String[] userName and organization
     */
    public function loadUser($id);
    
    /**
     * Get a User's password hash from the database
     * @param String $id User's id
     */
    public function getHash($id);

    //</editor-fold>

}

?>
