<?php

// If there is an ID attached, then we are being asked to update
// an existing element
function put($storage, $request) {
	$element = NULL;
	$response = NULL;
	if (NULL != $request->getId ()) {
		// Start by loading then deleting the element
		try {
			// TODO: Check that element types are the same before deleting
			$element = existingElementFactory ( $request->getId (), $storage );
		} catch ( Exception $e ) {
			// TODO: Element not found is expected
		}
		
		// Delete element if it was found
		if ($element) {
			// $element->delete();
			if ($request->getData ()["type"] == "DataFlow") {
				$element->setAssociativeArray ( $request->getData () );
			} else {
				$element->delete ();
			}
		}
	}
	
	if (NULL === $request->getData ()) {
		// TODO: More specific exception handling
		// Error response
		$response = new SimpleResponse ();
		$response->setRawData ( 'No data, bad request.' );
		$response->setHeader ( 400 );
	} else {
		$elementArray = $request->getData ();
		// TODO: Get actual list from database instead of hardcoding
		$validTypesArray = array (
				'Process',
				'Multiprocess',
				'ExternalInteractor',
				'DataStore',
				'DataFlowDiagram',
				'DataFlow' 
		);
		if (! in_array ( $elementArray ['type'], $validTypesArray )) {
			// Error response
			$response = new SimpleResponse ();
			$response->setRawData ( 'Element type: "' . $elementArray ['type'] . '" was invalid' );
			$response->setHeader ( 400 );
		} else {
			
			// The only time this should be null is for Diagram types
			$parentDia = $elementArray ['diagramId'];
			
			// Create a new element using the associative array
			if ($parentDia == NULL && $elementArray ['genericType'] != 'Diagram') {
				// TODO - send an unhappy header saying it was an element with no parent
			}
			
			// Create a new element, loading it from the element array
			// if ($elementArray['type'] == "DataFlow")
			// var_dump($request);
			$element = NULL;
			try {
				$element = new $elementArray ['type'] ( $storage, $elementArray );
			} catch ( Exception $e ) {
				// TODO: More specific exception handling
				// Error response
				$response = new SimpleResponse ();
				$response->setRawData ( $e->getMessage () );
				$response->setHeader ( 400 );
			}
			
			// Setup a response object with just a header
			if ($element) {
				$response = new SimpleResponse ( $element->getAssociativeArray () );
				$response->setHeader ( 201 );
			} else {
				$response = new SimpleResponse ();
				$response->setRawData ( "Element failed to initialize." );
				$response->setHeader ( 500 );
			}
		}
	}
	
	return $response;
}
