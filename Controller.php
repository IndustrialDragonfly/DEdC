<?php
/*
 * @author Eugene
 * Central piece of controller code which handles figuring out
 * how to handle all incoming requests from clients and addresses the data model
 * to deal with them.
 */



require_once 'ClassLoader.php';
require_once 'ExceptionHandler.php';
require_once "MethodsEnum.php";
require_once "conf.php";
require_once 'Methods/ElementFactory.php'; // Remove this one once the methods are updated.
require_once 'Methods/PUT.php';

set_exception_handler("ExceptionHandler");

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
     
    // Retrieve information about request and put it in a request object
    $body = file_get_contents('php://input'); // Get the body of the request
    $request = new SimpleRequest($_SERVER['HTTP_ACCEPT'], 
            $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $body);
        
    // Try to create a new User
    $user;
    try
    {
        $user = new User($storage, $request->getAuthenticationInfo());

    } 
    catch (Exception $ex) 
    {
        // If the User threw an exception, return the exception message.
        // TODO: Allow configuring of the response type
        // TODO: Make authentication error message extending from error
        $response = new SimpleErrorResponse();
        $response->setError($ex->getMessage());
        $response->setHeader(401);
        header($response->getHeader());           
        echo $response->getRepresentation();
        exit;
    }
    
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
                    $element = existingElementFactory($storage, $request->getId());
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
            // TODO: Allow configuring of the response type
            $response = new SimpleErrorResponse();
            $response->setError("Invalid HTTP Method. UPDATE is not supported.");
            $response->setHeader(405);
            header($response->getHeader());           
            echo $response->getRepresentation();
            break;

        
        case MethodsEnum::PUT:
        	$response = put($storage, $request);
        		
        	
            // Return the header
            header($response->getHeader());           
            echo $response->getRepresentation();
            break;


        case MethodsEnum::DELETE:
            // Delete needs to send no data other than a header
            $element = NULL;
            $response = new SimpleResponse();;
            if (NULL != $request->getId())
            {
                // Start by loading then deleting the element
                try 
                {
                    // TODO: Check that element types are the same before deleting
                    $element = existingElementFactory($storage, $request->getId());
                } 
                catch (Exception $e) 
                {
                    // TODO: Narrow down exception to handle 404 case only
                }

                // Delete element if it was found
                if ($element)
                {
                    $element->delete();
                    $response->setHeader(200);

                }
                else
                {
                    $response->setHeader(404);
                }
            }

            header($response->getHeader());
            break;


        case MethodsEnum::PATCH:
            // TODO: Allow configuring of the response type
            $response = new SimpleErrorResponse();
            $response->setError("Invalid HTTP Method. PATCH is not supported.");
            $response->setHeader(405);
            header($response->getHeader());           
            echo $response->getRepresentation();
            break;


        default:
            // TODO: Allow configuring of the response type
            $response = new SimpleErrorResponse();
            $response->setError("Invalid HTTP Method.");
            $response->setHeader(405);
            header($response->getHeader());           
            echo $response->getRepresentation();
            break;
    }