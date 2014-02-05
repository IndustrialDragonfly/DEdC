<?php
require_once 'Requestable.php';

/**
  * Abstract object which parses the incoming requests objects and provides
 * getters for all the data contained within
 *
 * @author eugene
 */
require_once 'MethodException.php';

abstract class Request implements Requestable {
    //<editor-fold desc="Attributes" defaultstate="collapsed">
    protected $method; // HTTP method in use, stored as a MethodsEnum type
                       // but looks like an int here
    /**
     * The URI given for the location, example: "/someplace/in/server"
     * @access protected
     * @var string
     */
    protected $id; 
    protected $path; // Path to the object from a root DFD
    protected $query; // Query data from the URL (optional)
    private $uuidTag = "_id"; // Tag that identifies a UUID
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
    protected function setId($id) 
    {
        $idLength = strlen($id);
        $tagLength = strlen($this->uuidTag);
        $this->id = substr($id, 0, $idLength - $tagLength);
    }
    protected function setAcceptTypes($type)
    {
        $this->type = $type;
    }
    protected function setPath($path)
    {
        $this->path = $path;
    }
    //</editor-fold>
    
    //<editor-fold desc="Getter functions" defaultstate="collapsed">
    public function getMethod()
    {
        return $this->method;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getAcceptTypes()
    {
        return $this->accept;
    }
    public function getPath()
    {
        return $this->path;
    }
    //</editor-fold>
    /**
     * Creates a new request object from data from the HTTP reuest, and puts
     * it into a convient form for use by the controller.
     * 
     * @param String $accept
     * @param String $method
     */
    public function __construct($accept, $method, $uri)
    {
        // Save method type used to access (from enum)
        $this->setMethod($method);
        // Save the acceptable types
        $delim = ", "; // Delimiter between acceptable content types
        $this->accept = explode($delim, $accept);
        
        // Figure out if URI is UUID or path
        
        // If it is a UUID, it should have the uuidTag on it
        if (FALSE !== stripos($uri, $this->uuidTag))
        {
            // Get the last / in the URI, and return everything after it
            $uriId = substr($uri, strrpos($uri, "/"));
            $this->setId($uriId);
            $this->setPath(NULL);
        }
        // If it is a path, set it as the path
        else 
        {
            $this->setPath($uri);
            $this->setId(NULL);
        }
    }
}
