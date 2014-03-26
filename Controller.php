<?php
/*
 * @author Eugene
 * Central piece of controller code which handles figuring out
 * how to handle all incoming requests from clients and addresses the data model
 * to deal with them.
 */

require_once "MethodsEnum.php";
require_once "conf.php";
require_once 'Methods/ElementFactory.php'; // Remove this one once the methods are updated.
require_once 'Methods/PUT.php';
require_once 'Methods/GET.php';
require_once 'Methods/DELETE.php';

require_once 'ExceptionHandler.php';
set_exception_handler("ExceptionHandler");

// Classloaders of the various components
require_once 'ClassLoader.php';
require_once 'Models/ClassLoader.php';
require_once 'Models/DataModel/ClassLoader.php';
require_once 'Models/UserModel/ClassLoader.php';
require_once 'AuthenticationInformation/ClassLoader.php';
require_once 'Storage/ClassLoader.php';
require_once 'Interface/ClassLoader.php';

spl_autoload_register("DataModelClassLoader");
spl_autoload_register("ModelsClassLoader");
spl_autoload_register("InterfaceClassLoader");
spl_autoload_register("StorageClassLoader");
spl_autoload_register("UserModelClassLoader");
spl_autoload_register("AuthenicationInformationClassLoader");
// Must always be last in the stack so that it can handle failure to load
spl_autoload_register("GeneralLoader");

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
            $response = get($storage, $request);
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
            $response = delete($storage, $request);
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