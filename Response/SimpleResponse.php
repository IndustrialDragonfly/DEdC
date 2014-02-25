<?php
require_once 'Response.php';
require_once 'GETResponsable.php';
require_once 'DELETEResponsable.php';
/**
 * Simple class to allow for testing of the abstract Request object.
 *
 * @author eugene
 */

final class SimpleResponse extends Response implements GETResponsable, DELETEResponsable
{
    /**
     * Contains the raw (associative array) data for the body.
     * @var String
     */
    private $rawData;
    
    /**
     * Contains the SimpleMediaType representation of the object being sent
     * @var String
     */
    private $representation;
    
    /**
     * Constructs a SimpleMediaType response, with the input consisting of
     * an associative array that contains the information for an entity of the
     * data model
     * @param Mixed[] $data
     */
    public function __construct()
    {
        // Constructor when passed data i.e. GET
        if (func_num_args() == 1 && is_array(func_get_arg(0)))
        {
            $this->setRawData(func_get_arg(0));
        }
        // If there is no data, don't do anything, header is set separately
    }
    
    /**
     * Sets the raw (associative array) data from the data model
     * 
     * @param Mixed[] $data
     */
    public function setRawData($data)
    {
        $this->rawData = $data;
    }
    
    /**
     * Converts the raw data into the SimpleMediaType representation.
     * Just switches between templates for different objects, hopefully
     * more sophisticated media types can be created in a cleaner way.
     */
    public function createRepresentation()
    {
        $this->representation = json_encode(addTags($this->rawData, $this->uuidTag));
    }
    
    /**
     * Returns the SimpleMediaType representation of the data to send to the 
     * client.
     * 
     * @return String
     */
    public function getRepresentation()
    {
        return $this->representation;
    }
}
