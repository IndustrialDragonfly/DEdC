<?php

/*
 * This PHP library is responsible for performing generic management tasks
 * on the associative arrays used by request/response objects.
 */

/**
 * For the input associative array, adds the UUID tag to relavent fields based 
 * on its genericType attribute.
 * @param Mixed[] $rawData
 * @param String $tag
 * @return Mixed[]
 */
function addTags($rawData, $tag)
{
    switch($rawData['genericType'])
    {
        case "Diagram":
            $rawData['id'] = addTag($rawData['id'], $tag);
            foreach ($rawData['nodeList'] as &$node)
            {
                $node['id'] = addTag($node['id'], $tag);
            }

            foreach ($rawData['linkList'] as &$link)
            {
                $link['id'] = addTag($link['id'], $tag);
                $link['originNode'] = addTag($link['originNode'], $tag);
                $link['destinationNode'] = addTag($link['destinationNode'], $tag);
            }

            foreach ($rawData['DiaNodeList'] as &$diaNode)
            {
                $diaNode['id'] = addTag($diaNode['id'], $tag);
            }

            for ($i = 0; $i < count($rawData['ancestry']); $i++)
            {
                $rawData['ancestry'][$i] = addTag($rawData['ancestry'][$i], $tag);
            }

            $rawData['diaNode'] = addTag($rawData['diaNode'], $tag);
            break;
           
        case "diaNode":
            // Intentionally let it fall through to Node so that we only
            // have to write linkList code here once
        case "Node":
            $rawData['id'] = addTag($rawData['id'], $tag);
            $rawData['diagramId'] = addTag($rawData['diagramId'], $tag);
            foreach ($rawData['linkList'] as &$link)
            {
                $link['id'] = addTag($link['id'], $tag);
                $link['originNode'] = addTag($link['originNode'], $tag);
                $link['destinationNode'] = addTag($link['destinationNode'], $tag);
            }
            break;
        
        case "Link":
            $rawData['id'] = addTag($rawData['id'], $tag);
            $rawData['diagramId'] = addTag($rawData['diagramId'], $tag);
            $rawData['originNode']['id'] = addTag($rawData['originNode']['id'], $tag);
            $rawData['destinationNode']['id'] = addTag($rawData['destinationNode']['id'], $tag);
            break;
            
        case "List":
            for ($i = 0; $i < count($rawData['list']); $i++)
            {
                $rawData['list'][$i]['id'] = addTag($rawData['list'][$i]['id'], $tag);
            }
            break;
        
        default:
            // Probably should update to a specialized exception
            throw new BadFunctionCallException("Not a valid genericType");
            break;
        
    }
    
    return $rawData;
}

/**
 * For the input associative array, strips the UUID tag from relevant fields
 * based on its genericType attribute.
 * @param Mixed[] $rawData
 * @param String $tag
 * @return Mixed[]
 */
function stripTags($rawData, $tag)
{
    // Find the length of an ID with no tag
    $idLength = strlen($rawData['id']) - strlen($tag);
    
    // One thing that always holds true is it has an id, so set it here
    $rawData['id'] = stripTag($rawData['id'], $idLength);
    
    switch($rawData['genericType'])
    {
        case "Diagram":
            foreach ($rawData['nodeList'] as &$node)
            {
                $node['id'] = stripTag($node['id'], $idLength);
            }

            foreach ($rawData['linkList'] as &$link)
            {
                $link['id'] = stripTag($link['id'], $idLength);
                $link['originNode'] = stripTag($link['originNode'], $idLength);
                $link['destinationNode'] = stripTag($link['destinationNode'], $idLength);
            }

            foreach ($rawData['DiaNodeList'] as &$diaNode)
            {
                $diaNode['id'] = stripTag($diaNode['id'], $idLength);
                $diaNode['childDiagramId'] = stripTag($diaNode['childDiagramId'], $idLength);
            }

            for ($i = 0; $i < count($rawData['ancestry']); $i++)
            {
                $rawData['ancestry'][$i] = stripTag($rawData['ancestry'][$i], $idLength);
            }

            $rawData['diaNode'] = stripTag($rawData['diaNode'], $idLength);
            break;
           
        case "diaNode":
            // Intentionally let it fall through to Node so that we only
            // have to write linkList code here once
            $rawData['childDiagramId'] = stripTag($rawData['childDiagramId'], $idLength);
        case "Node":
            $rawData['diagramId'] = stripTag($rawData['diagramId'], $idLength);
            foreach ($rawData['linkList'] as &$link)
            {
                $link['id'] = stripTag($link['id'], $idLength);
                $link['originNode'] = stripTag($link['originNode'], $idLength);
                $link['destinationNode'] = stripTag($link['destinationNode'], $idLength);
            }
            break;
        
        case "Link":
            $rawData['diagramId'] = stripTag($rawData['diagramId'], $idLength);
            $rawData['originNode']['id'] = stripTag($rawData['originNode']['id'], $idLength);
            $rawData['destinationNode']['id'] = stripTag($rawData['destinationNode']['id'], $idLength);
            break;
        
        default:
            // Probably should update to a specialized exception
            throw new BadFunctionCallException("Not a valid genericType");
            break;
        
    }
    
    return $rawData;
}

/**
 * For an input ID with a tag, it returns just the ID, assuming the the length
 * of the ID is set.
 * @param String $id
 * @param Int $length
 * @return String
 */
function stripTag($id, $length)
{
    if (func_num_args() != 2)
    {
        throw new BadFunctionCallException("Not the correct number of inputs to stripTag");
    }
    
     // Return null if empty rather than attempting to strip the tag
    if ($id == NULL)
    {
        return NULL;
    }
    
    // Check types of parameters
     if (!is_string($id) || !is_int($length))
    {
        throw new BadFunctionCallException("Invalid parameter type.");
    }
    
    return substr($id, 0, $length);
}

/**
 * Returns the id with the tag added. If null, returns null.
 * @param String $id
 * @param String $tag
 */
function addTag($id, $tag)
{
    if (func_num_args() != 2)
    {
        throw new BadFunctionCallException("Not the correct number of inputs to stripTag");
    }
    
    // Check if id is null
    if ($id == NULL)
    {
        return NULL;
    }
    
    // Check types of parameters
    if (!is_string($id) || !is_string($tag))
    {
        throw new BadFunctionCallException("Invalid parameter type.");
    }
    
    // Return input with tag appended
    return $id . $tag;
}

/**
 * Determines if the given parameter is a UUID. Returns True if "_id" is 
 * present at the end of the given parameter, False otherwise.
 * @param String $resource
 * @return Bool
 */
function isUUID($resource)
{
    if (func_num_args() != 1)
    {
        throw new BadFunctionCallException("isUUID expects 1 argument.");
    }
    
    $resourceLength = strlen($resource);
    $tag = substr($resource, $resourceLength - strlen("_id"), $resourceLength);

    if ($tag == "_id")
    {
        return true;
    }
    else
    {
        return false;
    }
}
?>