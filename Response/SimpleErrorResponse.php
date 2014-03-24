<?php
/**
 * Response type for error messages in SimpleMediaType.
 * @author eugene
 */
class SimpleErrorResponse extends Response implements ErrorResponsable
{
    /**
     * Sets the error message to send back to the client.
     * @param String $message
     */
    public function setError($message)
    {
        $this->rawData = array('Error' => $message);
        $this->representation = json_encode($this->rawData);
    }   
    
    /**
     * Returns the SimpleMediaType error message
     * @return String
     */
    public function getRepresentation()
    {
        return $this->representation;
    }
}
