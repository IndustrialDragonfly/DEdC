<?php
/*
 * @author Eugene
 * @Description Central piece of controller code which handles figuring out
 * how to handle all incoming requests from clients and addresses the data model
 * to deal with them.
 */

/**
 * Creates an element object from an object already in storage.
 * @param String $id
 * @param Readable and Writable $storage
 * @return \elementType
 */
function existingElementFactory($id, $storage)
{
    // Construct object that has been requested
    $elementType = $storage->getTypeFromUUID($id);
    $element = new $elementType($storage, $id);
    return $element;
}

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
 * relevant folders - those of the data models, those of Request and Response
 * folders, and those in the Storage folder
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
    elseif (file_exists("Storage/" . $classname . ".php"))
    {
        require_once "Storage/" . $classname . ".php";
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
    $body = file_get_contents('php://input'); // Get the body of the request
    $request = new SimpleRequest($_SERVER['HTTP_ACCEPT'], 
            $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $body);
    
    // Initialize a storage object
    $storage = new DatabaseStorage(); 
    
    switch ($request->getMethod())
    {
        case MethodsEnum::GET:
            $element = existingElementFactory($request->getId(), $storage);
            $response = new SimpleResponse($element->getAssociativeArray());
            $response->setHeader(200);
            header($response->getHeader());
            echo $response->getRepresentation();
            break;
        case MethodsEnum::POST:
            sendHeader(successful);
            // If needed
            sendData(result);
            break;
        case MethodsEnum::PUT:
          
            // If there is an ID attached, then we are being asked to update
            // an existing element
            if (NULL != $request->getId)
            {
                // Start by loading then deleting the element
                $element = existingElementFactory($request->getId(), $storage);
                $element->delete();
            }
            $elementArray = $request->getData();
            
            // The only time this should be null is for Diagram types
            $parentDia = $elementArray['parent'];
            
            // Create a new element using the associative array
            if ($parentDia == NULL && $elementArray['genericType'] != 'Diagram')
            {
                // TODO - send an unhappy header saying it was an element with no parent
            }
            
            // Create a new element, loading it from the element array
            $element = new $elementArray['type']($storage, $elementArray);
            $element->save();
            
            // Setup a response objcet with just a header
            $response = new SimpleResponse();
            $response->setHeader(200);
            // Return the header
            header($response->getHeader());           
                        
            break;
        case MethodsEnum::DELETE:
            // Delete needs to send no data other than a header
            $element = existingElementFactory($request->getId(), $storage);
            $element->delete();
            $response = new SimpleResponse();
            $response->setHeader(200);
            header($response->getHeader());
            break;
            break;
        case MethodsEnum::UPDATE:
            sendHeader(sucessful);
            // should be no need to send data since it is idemnipotentent
            break;
        default:
            echo "ERROR  - Bad method";
            //sendHeader(serverError);
            // Send server error here, because if this point has been hit
            // then something was wrong in the validation code (though this could
            // be a client error depending on how you look at it)            
    }