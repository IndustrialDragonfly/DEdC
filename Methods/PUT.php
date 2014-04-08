<?php

/**
 * Handle a PUT request
 * @param {ReadStorable,WriteStorable} $storage
 * @param SimpleRequest $request
 * @throws BadFunctionCallException
 * @return SimpleResponse
 */
function put($storage, $user,  $request) {
	$response = new SimpleResponse();
	
	// Cache the request's body
	$bodyArray = $request->getData();
	
	if ($request->getId() != NULL &&  $bodyArray != NULL)
	{ // Receiving an existing object
		// TODO Use getTypeByUUID to find out if the entity exists thus saving an SQL query
		if ($storage->entityExists($request->getId()))
		{		    
			// If the entity exists, load it and update it.
			if ($storage->getTypeFromUUID($request->getId()) == $bodyArray['type'])
			{
			    // Lock the Entity, throws exception if it is already locked
			    $storage->setLock($request->getId());
			     
				$element = existingElementFactory($storage, $user,  $request->getId());
				$element->setAssociativeArray($bodyArray);
				$response->setRawData($element->getAssociativeArray());
				$response->setHeader(201);
				
				$storage->releaseLock($request->getId());
			}
			else
			{			     
				// The new element's type different from what was stored.
				// TODO Send correct HTTP status code
				throw new BadFunctionCallException("Requested type did not match stored type.");
			}
		}
		else
		{
			// An ID was requested, but it was not found in the database.
			// TODO Send correct HTTP status code
			throw new BadFunctionCallException("No such ID.");
		}
	}
	else if ($request->getId() == NULL && $bodyArray)
	{ // Receiving new object
		$type = $bodyArray['type'];
		
		// TODO Call database for these
		$validTypesArray = array (
				'Process',
				'Multiprocess',
				'ExternalInteractor',
				'DataStore',
				'DataFlowDiagram',
				'DataFlow'
		);
		
		// Check that the received type was valid
		if (in_array($type, $validTypesArray))
		{
			$element = new $type($storage, $user,  $bodyArray);
			$response->setRawData($element->getAssociativeArray());
			$response->setHeader(201);
		}
		else
		{
			// TODO Send correct HTTP status code
			throw new BadFunctionCallException("Type was not valid");
		}
	}
	else
	{ // Error state
		// TODO Send correct HTTP status code
		throw new BadFunctionCallException("Request was not formed correctly.");
	}
	
	// Return the response
	return $response;
}
