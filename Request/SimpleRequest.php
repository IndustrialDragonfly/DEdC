<?php
require_once "Request.php";
require_once 'GETRequestable.php';
require_once 'DELETERequestable.php';

/**
 * Simple request object for testing purposes only.
 *
 * @author eugene
 */
final class SimpleRequest extends Request implements GETRequestable, DELETERequestable
{
    public function __construct($accept, $method, $uri)
    {
        parent::__construct($accept, $method, $uri);
    }
}
?>