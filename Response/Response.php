<?php
/**
 * Abstract object which provides the response to send to a client
 *
 * @author eugene
 */
abstract class Response
{
    //<editor-fold desc="Attributes" defaultstate="collapsed">
    /**
     * Header string, should have a valid response code to send
     * back to client and any other necessary information.
     * @var String
     */
    protected $header;
    /**
     * Contains data for the body.
     * @var String
     */
    protected $body;
    /**
     * The element object being returned, can be a node, DFD, or dataflow
     * Optional
     * @var Element
     */
    protected $element;    
    
    public function __construct($element) {
        $this->setElement($element);
    }
    
    //</editor-fold>
    
    //<editor-fold desc="Setters" defaultstate="collapsed">
    /**
     * Sets the header based on the specified status code.
     * For a list of status codes and their full definitions, see
     * http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
     * 
     * @param String $header
     */
    public function setHeader($code)
    {
        // Protocol info
        if (isset($_SERVER['SERVER_PROTOCOL']))
        {
            $proto = $_SERVER['SERVER_PROTOCOL'];
        }
        else
        {
            $proto = "HTTP/1.0";
        }   
        $header = $proto;
        switch ($code)
        {
            //<editor-fold desc="1xx Informational" defaultstate="collapsed">
            //</editor-fold>
            
            //<editor-fold desc="2xx Successful" defaultstate="collapsed">
            case 200:
                $header = $header . " 200 OK";
            //</editor-fold>

            //<editor-fold desc="3xx Redirection" defaultstate="collapsed">
            //</editor-fold>

            //<editor-fold desc="4xx Client Error" defaultstate="collapsed">
            //</editor-fold>

            //<editor-fold desc="5xx Server Error" defaultstate="collapsed">
            //</editor-fold>
        }
        $this->header = $header;
    }
    /**
     * Sets the element that is being returned to the client
     * @param Element Object $element
     */
    protected function setElement($element)
    {
        $this->element = $element;
    }
    /**
     * Sets the body function in whatever data format it is passed.
     * @param String $body
     */
    protected function setBody($body)
    {
        $this->body = $body;
    }
    //</editor-fold>
    
    //<editor-fold desc="Getters" defaultstate="collapsed">
    /**
     * Returns the header to be sent to the client
     * @return String
     */
    public function getHeader()
    {
        return $this->header;
    }
    
    /**
     * Returns the data for the body.
     * @return String
     */
    public function getBody()
    {
        return $this->body;
    }
    /**
     * Returns the element that is to be converted into a different format and
     * returned to the client
     * @return Element
     */
    public function getElement()
    {
        return $this->element;
    }
    //</editor-fold>
}

?>
