<?php

/**
 * Description of request
 * 
 * Abstract object which parses the incoming requests objects and provides
 * getters for all the data contained within
 *
 * @author eugene
 */
abstract class request {
    //<editor-fold desc="Attributes" defaultstate="collapsed">
    protected $method; // HTTP method in use
    protected $identifier; // identifier that the URL requested
    protected $query; // Query data from the URL (optional)
    protected $body; // Contains the body of the message (optional)
    protected $type; // Media type the request in/has requested
    //</editor-fold>
    
    //<editor-fold desc="Setter functions" defaultstate="collapse">
    protected function setMethod($method) {
        $this->method = $method;
    }
    protected function setIdentifier($identifier) {
        $this->identifier = $identifier;
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
    
    //<editor-fold desc="Getter functions" defaultstate="collapse">
    public function getMethod() {
        return $this->method;
    }
    public function getIdentifier()
    {
        return $this->identifier;
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
    //</editor-fold>
}
