<?php
/**
 * MethodException is when an exception occurs related to the type of HTTP 
 * method that a request/response object has been passed.
 *
 * @author eugene
 */
class MethodException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

?>
