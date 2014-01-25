<?php
/**
 * The WriteStorable interface defines the functions that a write storage class
 * must implement for use with DEdC. Its counterpart is the ReadStorable
 * class, which will define the functions required for a read storage class.
 * It is not required that both interfaces be implemented, allowing for the 
 * creation of write or read only media, such as for exporting to formats
 * like PDF
 * @author eugene
 */
interface WriteStorable
{
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
