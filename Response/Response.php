<?php
require_once 'Responsable.php';

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
    protected $uuidTag = "_id"; // Tag that identifies a UUID
    
    
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
            case 100:
                $header = $header . " 100 Continue";
                break;
            case 101:
                $header = $header . " 101 Switching Protocols";
                break;
            //<editor-fold desc="2xx Successful" defaultstate="collapsed">
            case 200:
                $header = $header . " 200 OK";
                break;
            case 201:
                $header = $header . " 201 Created";
                break;
            case 202:
                $header = $header . " 202 Accepted";
                break;
            case 203:
                $header = $header . " 203 Non-Authoritative Information";
                break;
            case 204:
                $header = $header . " 204 No Content";
                break;
            case 205:
                $header = $header . " 205 Reset Content";
                break;
            case 206:
                $header = $header . " 206 Partial Content";
                break;
            case 226:
                $header = $header . " 226 IM Used";
                break;
            //</editor-fold>

            //<editor-fold desc="3xx Redirection" defaultstate="collapsed">
            case 300:
                $header = $header . " 300 Multiple Choices";
                break;
            case 301:
                $header = $header . " 301 Moved Permanently";
                break;
            case 302:
                $header = $header . " 302 Found";
                break;
            case 303:
                $header = $header . " 303 See Other";
                break;
            case 304:
                $header = $header . " 304 Not Modified";
                break;
            case 305:
                $header = $header . " 305 Use Proxy";
                break;
            case 306:
                $header = $header . " 306 Switch Proxy";
                break;
            case 307:
                $header = $header . " 307 Temporary Redirect";
                break;
            case 308:
                $header = $header . " 308 Permanent Redirect";
            //</editor-fold>

            //<editor-fold desc="4xx Client Error" defaultstate="collapsed">
            case 400:
                $header = $header . " 400 Bad Request";
                break;
            case 401:
                $header = $header . " 401 Unauthorized";
                break;
            case 402:
                $header = $header . " 402 Payment Required";
                break;
            case 403:
                $header = $header . " 403 Forbidden";
                break;
            case 404:
                $header = $header . " 404 Not Found";
                break;
            case 405:
                $header = $header . " 405 Method Not Allowed";
                break;
            case 406:
                $header = $header . " 406 Not Acceptable";
                break;
            case 407:
                $header = $header . " 407 Proxy Authentication Required";
                break;
            case 408:
                $header = $header . " 408 Request Timeout";
                break;
            case 409:
                $header = $header . " 409 Conflict";
                break;
            case 410:
                $header = $header . " 410 Gone";
                break;
            case 411:
                $header = $header . " 411 Length Required";
                break;
            case 412:
                $header = $header . " 412 Precondition Failed";
                break;
            case 413:
                $header = $header . " 413 Request Entity Too Large";
                break;
            case 414:
                $header = $header . " 414 Request-URI Too Long";
                break;
            case 415:
                $header = $header . " 415 Unsupported Media Type";
                break;
            case 416:
                $header = $header . " 416 Requested Range Not Satisfiable";
                break;
            case 417:
                $header = $header . " 417 Expectation Failed";
                break;
            case 418:
                $header = $header . " 418 I'm a teapot";
                break;
            case 419:
                $header = $header . " 419 Authentication Timeout ";
                break;
            case 426:
                $header = $header . " 426 Upgrade Required";
                break;
            case 428:
                $header = $header . " 428 Precondition Required";
                break;
            case 429:
                $header = $header . " 429 Too Many Requests";
                break;
            case 431:
                $header = $header . " 431 Request Header Fields Too Large";
                break;

            //</editor-fold>

            //<editor-fold desc="5xx Server Error" defaultstate="collapsed">
            case 500:
                $header = $header . " 500 Internal Server Error";
                break;
            case 501:
                $header = $header . " 501 Not Implemented";
                break;
            case 503:
                $header = $header . " 503 Service Unavailable";
                break;
            case 505:
                $header = $header . " 505 HTTP Version Not Supported";
                break;
            case 506:
                $header = $header . " 506 Variant Also Negotiates";
                break;
            case 510:
                $header = $header . " 510 Not Extended";
                break;
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
