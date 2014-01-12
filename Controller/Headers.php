<?php
/**
 * Headers provides values for the status codes from headers used 
 * in DEdC
 * 
 * For a list of status codes and their full definitions, see
 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
 *
 * @author eugene
 * 
 * @param int $code
 * @return string
 */
function header_codes($code)
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
    return $header;
}
?>
