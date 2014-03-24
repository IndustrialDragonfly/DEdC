<?php

/**
 * Custom exception handler for DEdC. Creates a response object with the error
 * message.
 */

function ExceptionHandler($exception)
{
    // Create the exception response
    // If the User threw an exception, return the exception message.
    // TODO: Allow configuring of the response type
    $response = new SimpleErrorResponse();
    $response->setError($exception->getMessage());
    // Since issue is unknown, just an internal server error
    $response->setHeader(500);
    header($response->getHeader());
    // Provide exception error to frontend
    echo $response->getRepresentation();
}