<?php
/**
 * Handle a DELETE request
 * @param {ReadStorable,WriteStorable} $storage
 * @param SimpleRequest $request
 * @throws BadFunctionCallException
 * @return SimpleResponse
 */
function delete($storage, $request) 
{
	// TODO Update errors to be exceptions that will be handled by the Controller
	
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
	
	return $response;
}
