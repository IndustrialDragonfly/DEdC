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

/*
 * Checks to see if the incoming request has the proper user agent product,
 * if it does not loads the web client page.
 */
$web_client_location = "Frontend/dist/";
$browser_accept = '*/*';
if (FALSE !== stripos($_SERVER['HTTP_ACCEPT'], $browser_accept))
{
    require_once $web_client_location."/index.php";
    webClient($web_client_location);
    exit;
}

/**
 * Autoload function which loads a file based on class name from any of the 
 * relevant folders - those of the data models and those of Request and Response
 * folders.
 * @param String $classname
 */
function __autoload($classname)
{
    if (file_exists($classname . ".php") )
    {
        require_once $classname . ".php";
    }
    elseif (file_exists("Request/" . $classname . ".php"))
    {
        require_once "Request/" . $classname . ".php";
    }
    elseif (file_exists("Response/" . $classname . ".php"))
    {
        require_once "Response/" . $classname . ".php";
    }
    elseif (file_exists("DFDModel/" . $classname . ".php"))
    {
        require_once "DFDModel/" . $classname . ".php";
    }
    else
    {
        // Make throw an exception later
        echo "Problem loading file";
        exit;
    }
}

require_once "MethodsEnum.php";
require_once "Authentication.php";
require_once "AuthorizeUser.php";
require_once 'Storage/DatabaseStorage.php';


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
    /*if (!checkFormat())
    {
        // Return error indicating problem with format to client
        sendHeader(invalidFormat);
        exit;
    }*/
    // Determine if user has correct permissions to perform the action
    if (!authorizeUser())
    {
        // Return authorization error to client
        sendHeader(notAuthorized);
        exit;
    }
    
    // Retrieve information about request and put it in a request object
    $request = new SimpleRequest($_SERVER['HTTP_ACCEPT'], $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
    
    // Demonstrate ability to retrieve the type of an existing element
    // from the DB. Not really controller code, just a convient place to
    // demo, absolutely should be deleted soon
    $storage_access = new DatabaseStorage();
    $test_element = new Process();
    $test_element->save($storage_access);
    $id = $test_element->getId();
    $test_type = $storage_access->getTypeFromUUID($id);
    echo $test_type;
    
    
    
    switch ($request->getMethod())
    {
        case MethodsEnum::GET:
            $response = new SimpleResponse();
            $response->setHeader(200);
            header($response->getHeader());
            echo $response->getBody();
            break;
        case MethodsEnum::POST:
            sendHeader(successful);
            // If needed
            sendData(result);
            break;
        case MethodsEnum::PUT:
            sendHeader(sucessful);
            // Should be no need to send data since it is idemnipotentent
            break;
        case MethodsEnum::DELETE:
            sendHeader(sucessful);
            // should be no need to send data since it is idemnipotentent
            break;
        case MethodsEnum::UPDATE:
            sendHeader(sucessful);
            // Should need to send no data since it is idemnipotentent
            break;
        default:
            echo "ERROR  - Bad method";
            //sendHeader(serverError);
            // Send server error here, because if this point has been hit
            // then something was wrong in the validation code (though this could
            // be a client error depending on how you look at it)            
    }