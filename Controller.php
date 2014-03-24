<?php
/*
 * @author Eugene
 * Central piece of controller code which handles figuring out
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

require_once 'ClassLoader.php';
require_once "MethodsEnum.php";
require_once "conf.php";

    /*
    * Checks to see if the incoming request has the proper user agent product,
    * if it does not loads the web client page.
    */
   $web_client_location = "Frontend/";
   $browser_accept = '*/*';
   // TODO: Filter $_SERVER
   if (FALSE !== stripos($_SERVER['HTTP_ACCEPT'], $browser_accept))
   {
       require_once $web_client_location."/index.php";
       webClient($web_client_location);
       exit;
   }

    // Initialize a storage object
    $storage = new DatabaseStorage(); 

     // Instantiate a new authentication module as named by conf.php
    $authenticator = new $authenModule($storage);    
    
    // Pass authentication information from client
    if (!$authenticator->authenticate())
    {
        // TODO - handle authentication
        exit;
    }
     
    // Retrieve information about request and put it in a request object
    $body = file_get_contents('php://input'); // Get the body of the request
    $request = new SimpleRequest($_SERVER['HTTP_ACCEPT'], 
            $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $body);
    
    switch ($request->getMethod())
    {
        case MethodsEnum::GET:
            // Response to return at the end of this block
            $response;

            // If it is a request to a resource
            if (NULL != $request->getResource())
            {  
                if ("elements" == $request->getResource())
                {
                    // List of all elements
                    $elementArray = $storage->getListByType("*");
                    
                    if ($elementArray)
                    {
                        // Success response
                        $response = new SimpleResponse();
                        $response->setRawData($elementArray);
                        $response->setHeader(200);
                    }
                    else
                    {
                        // Fail response
                        $response = new SimpleResponse();
                        $response->setRawData("Could not complete request for \"elements\"");
                        $response->setHeader(400);
                    }
                }
                else
                {
                    // Get elements based on type such as DataFlowDiagram
                    // TODO: Check to see if the type is valid
                    try
                    {
                        $elementArray = $storage->getListByType($request->getResource());
                    } 
                    catch (Exception $ex) 
                    {
                        // Fail response
                        $response = new SimpleResponse();
                        $response->setRawData($e->getMessage());
                        $response->setHeader(400);
                    }
                    
                    if ($elementArray)
                    {
                        // Success response
                        $response = new SimpleResponse();
                        $response->setRawData($elementArray);
                        $response->setHeader(200);
                    }
                }
            }
            else if ($request->getId() != NULL)
            {
                // Get an Entity
                $element;
                try
                {
                    $element = existingElementFactory($request->getId(), $storage);
                }
                catch (Exception $e) // TODO: Make more specific catch cases
                {
                    // Error response
                    $response = new SimpleResponse();
                    $response->setRawData($e->getMessage());
                    $response->setHeader(404);
                }

                // Successful Response
                if (!isset($response))
                {
                    $response = new SimpleResponse($element->getAssociativeArray());
                    // TODO - handle fail cases
                    $response->setHeader(200);
                }
            }
            else
            {
                // No other action was choosen
                $response = new SimpleResponse();
                $response->setRawData("Request had no resource or id.");
                $response->setHeader(400);
            }
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
            $element;
            if (NULL != $request->getId())
            {
                // Start by loading then deleting the element
                try 
                {
                    // TODO: Check that element types are the same before deleting
                    $element = existingElementFactory($request->getId(), $storage);
                } 
                catch (Exception $e) 
                {
                    // TODO: Handle error exception
                }
                
                // Delete element if it was found
                if ($element)
                {
                    $element->delete();
                }
            }
            
            if (NULL === $request->getData())
            {
                // TODO: More specific exception handling
                // Error response
                $response = new SimpleResponse();
                $response->setRawData('No data, bad request.');
                $response->setHeader(400);
            }
            else
            {
                $elementArray = $request->getData();
                // TODO: Get actual list from database instead of hardcoding
                $validTypesArray = array('Process', 'Multiprocess', 'ExternalInteractor', 'DataStore', 'DataFlowDiagram', 'DataFlow');
                if (!in_array($elementArray['type'], $validTypesArray))
                {
                    // Error response
                    $response = new SimpleResponse();
                    $response->setRawData('Element type: "' . $elementArray['type'] . '" was invalid');
                    $response->setHeader(400);
                } 
                else 
                {
                
                    // The only time this should be null is for Diagram types
                    $parentDia = $elementArray['parent'];

                    // Create a new element using the associative array
                    if ($parentDia == NULL && $elementArray['genericType'] != 'Diagram')
                    {
                        // TODO - send an unhappy header saying it was an element with no parent
                    }

                    // Create a new element, loading it from the element array
                    $element;
                    try
                    {
                        $element = new $elementArray['type']($storage, $elementArray);
                    }
                    catch (Exception $e)
                    {
                        // TODO: More specific exception handling
                        // Error response
                        $response = new SimpleResponse();
                        $response->setRawData($e->getMessage());
                        $response->setHeader(400);
                    }

                    // If the element was created, save it
                    if ($element)
                    {
                        $element->save();
                    }

                    // Setup a response object with just a header
                    $response = new SimpleResponse($element->getAssociativeArray());
                    $response->setHeader(201);
                }
            }
            
            // Return the header
            header($response->getHeader());           
            echo $response->getRepresentation();

            break;
            
            
        case MethodsEnum::DELETE:
            // Delete needs to send no data other than a header
            $element = existingElementFactory($request->getId(), $storage);
            $element->delete();
            // TODO - Handle fail cases
            $response = new SimpleResponse();
            $response->setHeader(404);
            header($response->getHeader());
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