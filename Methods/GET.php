<?php

/**
 * Handle a GET request
 * @param {ReadStorable,WriteStorable} $storage
 * @param SimpleRequest $request
 * @throws BadFunctionCallException
 * @return SimpleResponse
 */
function get($storage, $user, $request)
{
	// TODO Exceptions should be thrown back to the Controller, and handled from there.
	
	// Response to return at the end of this block
	$response;
	
	// If it is a request to a resource
	if (NULL != $request->getResource())
	{
		if ("elements" == $request->getResource())
		{
			// List of all elements
			$elementArray = $storage->getListByType("*", $user);
	
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
				$elementArray = $storage->getListByType($request->getResource(), $user);
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
		$element = false;
		$locked = false;
		try
		{
		    $locked = $storage->isLocked($request->getId());
		    if (!$locked)
		    {
			     $element = existingElementFactory($storage, $user,  $request->getId());
		    }
		}
		catch (Exception $e) // TODO: Make more specific catch cases
		{
			// Error response
			$response = new SimpleResponse();
			$response->setRawData($e->getMessage());
			$response->setHeader(404);
		}
		
		if ($locked)
		{
		    $response = new SimpleErrorResponse();
		    $response->setError("Entity was locked.");
		    $response->setHeader(409);
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
	
	return $response;
}