<?php
/**
 * Description of BadConstructorCallException
 *
 * @author Josh Clark
 */
class BadConstructorCallException extends BadFunctionCallException
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
?>
