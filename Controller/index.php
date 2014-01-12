<?php
/*
 * @author Eugene
 * @Description Central piece of controller code which handles figuring out
 * how to handle all incoming requests from clients and addresses the data model
 * to deal with them.
 * 
 * Currently is no more than a very rough skeleton with the aim of sparking
 * discussion on its design. 
 */
    // Decode URL if needed
    
    // Pass authentication information from client
    if (!authenticateUser())
    {
        // Return authentication error to client
        sendHeader(failedAuthentication);
        exit;
    }
    
    // Determine if a valid request and media type
    // apache_request_headers();
    if (!checkFormat())
    {
        // Return error indicating problem with format to client
        sendHeader(invalidFormat);
        exit;
    }
    // Determine if user has correct permissions to perform the action
    if (!authorizeUser())
    {
        // Return authorization error to client
        sendHeader(notAuthorized);
        exit;
    }
    
    // Retrieve information about action and put it into data structure (associative array)
    $request = getRequestInfo();
    
    // Request[type] should be an enum of available operations [get, put, etc]
    // Obviously cases will involve more processing and will have the ability to 
    // send a fail header if that situation arises
    // Also needs to address the problem of updates since the copy of the data
    // that the client has once we get to multi-user
    switch ($request[type])
    {
        case Types.GET:
            sendHeader(successful);
            sendData(result);
            break;
        case Types.POST:
            sendHeader(successful);
            // If needed
            sendData(result);
            break;
        case Types.PUT:
            sendHeader(sucessful);
            // Should be no need to send data since it is idemnipotentent
            break;
        case Types.DELETE:
            sendHeader(sucessful);
            // should be no need to send data since it is idemnipotentent
            break;
        case Types.UPDATE:
            sendHeader(sucessful);
            // Should need to send no data since it is idemnipotentent
            break;
        default:
            sendHeader(serverError);
            // Send server error here, because if this point has been hit
            // then something was wrong in the validation code (though this could
            // be a client error depending on how you look at it)            
    }