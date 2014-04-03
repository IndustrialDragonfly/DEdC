<?php
/**
 * Handle a DELETE request
 * @param {ReadStorable,WriteStorable} $storage
 * @param SimpleRequest $request
 * @throws BadFunctionCallException
 * @return SimpleResponse
 */
function delete($storage, $user, $request) 
{
	// TODO Update errors to be exceptions that will be handled by the Controller
	
	// Delete needs to send no data other than a header
	$element = NULL;
	$response = new SimpleResponse();;
	if (NULL != $request->getId())
	{
		// Start by loading then deleting the element
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
	
		// Delete element if it was found
		if ($element)
		{
			$element->delete();
			$storage->releaseLock($request->getId());
			$response->setHeader(200);
		}
	}
	
	return $response;
}
