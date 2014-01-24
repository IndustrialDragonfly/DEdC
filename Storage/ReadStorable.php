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
    /**
     * Returns the type of element from the data store based on the given resource
     * UUID
     * 
     * @param string $resource
     */
    public function getTypeFromUUID($resource);
    
    /**
     * Saves a given node object into the data store
     * 
     * @param String $resource
     * @param String $label
     * @param String $type
     * @param String $originator
     * @param int $x
     * @param int $y
     * @param String array $links
     */
    public function saveNode($resource, $label, $type, $originator, $x, $y, $links, $numLinks);
}

?>
