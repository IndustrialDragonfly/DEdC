<?php
require_once "Request.php";

/**
 * Simple request object for testing purposes only.
 *
 * @author eugene
 */
class SimpleRequest extends Request
{
    public function __construct($accept, $method, $uri)
    {
        parent::__construct($accept, $method, $uri);
    }
}
?>