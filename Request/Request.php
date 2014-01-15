<?php
/**
  * Abstract object which parses the incoming requests objects and provides
 * getters for all the data contained within
 *
 * @author eugene
 */
require_once 'MethodException.php';

abstract class Request {
    //<editor-fold desc="Attributes" defaultstate="collapsed">
    protected $method; // HTTP method in use, stored as a MethodsEnum type
                       // but looks like an int here
    /**
     * The URI given for the location, example: "/someplace/in/server"
     * @access protected
     * @var string
     */
    protected $resource; 
    protected $query; // Query data from the URL (optional)
    protected $body; // Contains the body of the message (optional)
    protected $type; // Media type the request in/has requested
        //<editor-fold desc="Header Attributes" defaultstate="expanded">
        /**
         * Array of the acceptable content types according to the client.
         * @var String Array
         */
        protected $accept; 
        
        //</editor-fold>
    //</editor-fold>
    
    //<editor-fold desc="Setter functions" defaultstate="collapsed">
    protected function setMethod($method) 
    {
        switch ($method)
        {
            case "GET":
                $this->method = MethodsEnum::GET;
                break;
            case "POST":
                $this->method = MethodsEnum::POST;
                break;
            case "PUT":
                $this->method = MethodsEnum::PUT;
                break;
            case "DELETE":
                $this->method = MethodsEnum::DELETE;
                break;
            case "PATCH":
                $this->method = MethodsEnum::PATCH;
                break;
            default:
                throw new MethodException($method." is not a valid HTTP method
                    for use with DEdC.");
                
        }
    }
    protected function setResource($resource) {
        $this->resource = $resource;
    }
    protected function setQuery($query)
    {
        $this->query = $query;
    }
    
    protected function setBody($body)
    {
        $this->body = $body;
    }
    protected function setType($type)
    {
        $this->type = $type;
    }
    //</editor-fold>
    
    //<editor-fold desc="Getter functions" defaultstate="collapsed">
    public function getMethod() {
        return $this->method;
    }
    public function getResource()
    {
        return $this->resource;
    }
    public function getQuery() {
        return $this->query;
    }
    public function getBody()
    {
        return $this->body;
    }
    public function getType()
    {
        return $this->type;
    }
    public function getAccept()
    {
        return $this->accept;
    }
    //</editor-fold>
    /**
     * Creates a new request object from data from the HTTP reuest, and puts
     * it into a convient form for use by the controller.
     * 
     * @param String $accept
     * @param String $method
     * @param String $resource
     */
    public function __construct($accept, $method, $resource)
    {
        // Save method type used to access (from enum)
        $this->setMethod($method);
        // Save the acceptable types
        $delim = ", "; // Delimiter between acceptable content types
        $this->accept = explode($delim, $accept);
        
        $this->setResource($resource);
    }
}
