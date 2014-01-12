<?php

/**
 * Description of request
 * 
 * Abstract object which parses the incoming requests objects and provides
 * getters for all the data contained within
 *
 * @author eugene
 */
abstract class Request {
    //<editor-fold desc="Attributes" defaultstate="collapsed">
    protected $method; // HTTP method in use
    protected $identifier; // identifier that the URL requested
    protected $query; // Query data from the URL (optional)
    protected $header; // Header from the client (required)
    protected $body; // Contains the body of the message (optional)
    protected $type; // Media type the request in/has requested
        //<editor-fold desc="Header Attributes" defaultstate="expanded">
        protected $accept; // Acceptable content types
        
        //</editor-fold>
    //</editor-fold>
    
    //<editor-fold desc="Setter functions" defaultstate="collapsed">
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
    
    protected function setHeader($header)
    {
        $this->header = $header;
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
    public function getIdentifier()
    {
        return $this->identifier;
    }
    public function getQuery() {
        return $this->query;
    }
    
    public function getHeader()
    {
        return $this->header;
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
    
    public function __construct($header)
    {
        // Save header
        $this->setHeader($header);
        $this->processHeader();
    }
    
    /**
     * processHeader reads the header and pulls out the individual pieces
     * of infomation from it, storing them into their own attributes
     */
    private function processHeader()
    {
        $this->accept = array();
        $acceptValues = $this->header['Accept'];
        $delim = ", "; // Delimiter between acceptable content types
        $curPos = 0; // Current start of content type
        $delimPos = strpos($acceptValues, $delim, $curPos);
        while (FALSE !== $delimPos)
        {
            array_push($this->accept, substr($acceptValues, $curPos, $delimPos - $curPos));
            $curPos = $delimPos + 2;
            $delimPos = strpos($acceptValues, $delim, $curPos);            
        }
        // Final read, gets last content type
        array_push($this->accept, substr($acceptValues, $curPos));
    }
}
