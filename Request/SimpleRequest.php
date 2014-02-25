<?php
require_once "Request.php";
require_once 'GETRequestable.php';
require_once 'DELETERequestable.php';
require_once 'PUTRequestable.php';

/**
 * Simple request object for testing purposes only.
 *
 * @author eugene
 */
final class SimpleRequest extends Request implements GETRequestable, DELETERequestable, PUTRequestable
{
    public function __construct($accept, $method, $uri, $rawData)
    {
        parent::__construct($accept, $method, $uri, $rawData);
        
        // Call the localized createAssociativeArray
        $this->associativeArray = $this->createAssociativeArray($this->rawData);
    }
    
    /**
     * For an input SimpleMediaType JSON string, converts it to an associative
     * array, then strips the tags off the IDs of that associative array, then
     * returns the final associative arary.
     * @param String $data
     * @return Mixed[]
     */
    private function createAssociativeArray($data)
    {
        if ($data != NULL)
        {
            // TODO: Catch false and null returns from json_decode
            $data = json_decode($data, true);
            return $data;
        }
        return NULL;
    }
}
?>