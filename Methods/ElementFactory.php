<?php
/**
 * Creates an element object from an object already in storage.
 * @param String $id
 * @param Readable and Writable $storage
 * @return \elementType
 */
function existingElementFactory($storage, $user, $id)
{
	// Construct object that has been requested
	$elementType = $storage->getTypeFromUUID($id);
	$element = new $elementType($storage, $user, $id);
	return $element;
}