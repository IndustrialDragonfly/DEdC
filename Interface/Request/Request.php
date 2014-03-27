<?php
require_once 'Interface/associativeArrayManager.php';

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
    protected $resource; // Path to resource
    private $uuidTag = "_id"; // Tag that identifies a UUID
    protected $rawData; // Data from the client
    protected $associativeArray; // Data after being processed into default form
    
    /**
     * Array of the acceptable content types according to the client.
     * @var String Array
     */
    protected $accept;
    
    protected $authenticationHandler;
    
    //</editor-fold>
    
    /**
     * Creates a new request object from data from the HTTP reuest, and puts
     * it into a convient form for use by the controller.
     * 
     * @param String $accept
     * @param String $method
     */
    public function __construct($accept, $method, $uri, $rawData)
    {
        // Save method type used to access (from enum)
        $this->setMethod($method);
        // Save the acceptable types
        $delim = ", "; // Delimiter between acceptable content types
        $this->accept = explode($delim, $accept);
        
        // Save the body data
        $this->setData($rawData);
        
        // Decodes the special characters in the URI
        $decodedUri = urldecode($uri);
        
        // Decode the query string from the url
        $assocArray = parse_url($decodedUri);
        if (isset($assocArray['query']))
        {
            // Convert queryString to an associative array
            $queryArray = NULL;
            parse_str($assocArray['query'], $queryArray);
            $authType = $queryArray["authType"];
            
            // TODO: Pass Controller error information
            if (is_subclass_of($authType, "AuthenticationHandleable"))
            {
                $this->authenticationHandler = new $authType($queryArray);
            }
        }

        // Figure out if URI is UUID or resource
        // If it is a UUID, it should have the uuidTag on it
        $tagPos = stripos($decodedUri, $this->uuidTag);
        if (FALSE !== $tagPos)
        {
            // Get the position of the beginning of the id
            $idPos = strrpos($assocArray['path'], "/");
                        
            // Get the last / in the URI, and return everything after it
            $uriId = substr($assocArray['path'], $idPos + 1);
            $this->setId($uriId);
            $this->setResource(NULL);
        }
        // If it is a resource, e.g. elements
        else 
        {
            $resourcePos = strrpos($assocArray['path'], "/");
            
            $resource = substr($assocArray['path'], $resourcePos + 1);
            $this->setResource($resource);
            $this->setId(NULL);
        }
    }
    
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
        if ($id !== NULL)
        {
            $idLength = strlen($id);
            $tagLength = strlen($this->uuidTag);
            $this->id = new ID($id);
        }
    }
    protected function setAcceptTypes($type)
    {
        $this->type = $type;
    }
    protected function setResource($resource)
    {
        $this->resource = $resource;
    }
    public function setData($data)
    {
        $this->rawData = $data;
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
    public function getResource()
    {
        return $this->resource;
    }
    public function getData()
    {
        return stripTags($this->associativeArray, $this->uuidTag);
    }
    
    /**
     * Get the AuthenticationInformation object
     * @return AuthenticationInformation
     */
    public function getAuthenticationInfo()
    {
    	// AuthenticationHandler will not created if no credentials are present in the query string
    	// Calling that object will result in PHP crashing.
    	if ($this->authenticationHandler)
    	{
    		return $this->authenticationHandler->getAuthenticationInfo();
    	}
    	else
    	{
    		return NULL;
    	}
    }
    //</editor-fold>
}
