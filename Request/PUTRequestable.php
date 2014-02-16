<?php
/**
 * Interface for PUT response objects to comply with
 * @author eugene
 */
interface PUTRequestable
{
    /**
     * Sets the data sent by the client
     * @param String $data
     */
    public function setData($data);
    
    /**
     * Gets the data after it has been converted to an associative array
     * return Mixed[]
     */
    public function getData();
}

?>
