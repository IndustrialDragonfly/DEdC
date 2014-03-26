<?php

//require_once 'ElementFactory.php';

// If there is an ID attached, then we are being asked to update
// an existing element
function put($storage, $request) {
	$response = new SimpleResponse();
	
	// Cache the request's body
	$bodyArray = $request->getData();
	
	if ($request->getId() != NULL &&  $bodyArray != NULL)
	{ // Receiving an existing object
		// TODO Use getTypeByUUID to find out if the entity exists thus saving an SQL query
		if ($storage->entityExists($request->getId()))
		{
			if ($storage->getTypeFromUUID($request->getId()) == $bodyArray['type'])
			{
				$element = existingElementFactory($storage, $request->getId());
				$element->setAssociativeArray($bodyArray);
				$response->setRawData($element->getAssociativeArray());
				$response->setHeader(201);
			}
			else
			{
				// TODO Send correct HTTP status code
				throw new BadFunctionCallException("Requested type did not match stored type.");
			}
		}
		else
		{
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
		
		if (in_array($type, $validTypesArray))
		{
			$element = new $type($storage, $bodyArray);
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
	
	return $response;
}
