<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

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
