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
     * Returns the type of element from the database based on the given resource
     * UUID
     * 
     * @param string $resource
     */
    public function getTypeFromUUID($resource);
}

?>
