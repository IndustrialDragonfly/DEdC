<?php
/**
 *
 * @author eugene
 */
interface GETResponsable
{
     /**
     * Sets the raw (associative array) data from the data model
     * 
     * @param Mixed[] $data
     */
    public function setRawData($data);
    
     /**
     * Returns the representation of the data to send to the client.
     * 
     * @return String
     */
    public function getRepresentation();
}

?>
