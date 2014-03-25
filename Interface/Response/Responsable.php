<?php
/**
 * Interface defining functions that will be required for all media types and
 * all HTTP methods response objects to have implemented.
 * 
 * @author eugene
 */
interface Responsable
{    
    /**
     * Sets the response header based on the input HTTP code.
     * 
     * @param int $code
     */
    public function setHeader($code);
    
    /**
     * Returns the header to send to the client.
     * 
     * @return String
     */
    public function getHeader();
}

?>
