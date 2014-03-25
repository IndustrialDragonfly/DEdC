<?php
/**
 * Interface for responses which handle errors.
 * 
 * @author Eugene Davis
 */
interface ErrorResponsable extends Responsable
{
    /**
     * Sets the error message to send back to the client.
     * @param String $message
     */
    public function setError($message);
    
    /**
     * Returns the SimpleMediaType error message
     * @return String
     */
    public function getRepresentation();
}
