<?php
/**
 * Abstract object which provides the response to send to a client
 *
 * @author eugene
 */
abstract class Response implements Responsable
{
    //<editor-fold desc="Attributes" defaultstate="collapsed">
    /**
     * Header string, should have a valid response code to send
     * back to client and any other necessary information.
     * @var String
     */
    protected $header;
    
    
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
    //</editor-fold>
}

?>
