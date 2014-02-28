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
    // TODO: Check if Ids are null first
    switch($rawData['genericType'])
    {
        case "Diagram":
            $rawData['id'] = $rawData['id'] . "$tag";
            foreach ($rawData['nodeList'] as &$node)
            {
                $node['id'] = $node['id'] . "$tag";
            }

            foreach ($rawData['linkList'] as &$link)
            {
                $link['id'] = $link['id'] . "$tag";
                $link['originNode'] = $link['originNode'] . "$tag";
                $link['destinationNode'] = $link['destinationNode'] . "$tag";
            }

            foreach ($rawData['DiaNodeList'] as &$diaNode)
            {
                $diaNode['id'] = $diaNode['id'] . "$tag";
            }

            for ($i = 0; $i < count($rawData['ancestry']); $i++)
            {
                $rawData['ancestry'][$i] = $rawData['ancestry'][$i] . "$tag";
            }

            $rawData['diaNode'] = $rawData['diaNode'] . "$tag";
            break;
           
        case "diaNode":
            $rawData['id'] = $rawData['id'] . "$tag";
            $rawData['diagramId'] = $rawData['diagramId'] . "$tag";
            // Intentionally let it fall through to Node so that we only
            // have to write linkList code here once
        case "Node":
            $rawData['id'] = $rawData['id'] . "$tag";
            foreach ($rawData['linkList'] as &$link)
            {
                $link['id'] = $link['id'] . "$tag";
                $link['originNode'] = $link['originNode'] . "$tag";
                $link['destinationNode'] = $link['destinationNode'] . "$tag";
            }
            break;
        
        case "Link":
            $rawData['id'] = $rawData['id'] . "$tag";
            foreach ($rawData['nodeList'] as &$node)
            {
                $node['id'] = $node['id'] . "$tag";
            }
            break;
            
        case "List":
            for ($i = 0; $i < count($rawData['list']); $i++)
            {
                $rawData['list'][$i]['id'] = $rawData['list'][$i]['id'] . "$tag";
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
    // TODO: Check if Ids are null first
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
            }

            for ($i = 0; $i < count($rawData['ancestry']); $i++)
            {
                $rawData['ancestry'][$i] = stripTag($rawData['ancestry'][$i], $idLength);
            }

            $rawData['diaNode'] = stripTag($rawData['diaNode'], $idLength);
            break;
           
        case "diaNode":
            $rawData['diagramId'] = stripTag($rawData['diagramId'], $idLength);
            // Intentionally let it fall through to Node so that we only
            // have to write linkList code here once
        case "Node":
            foreach ($rawData['linkList'] as &$link)
            {
                $link['id'] = stripTag($link['id'], $idLength);
                $link['originNode'] = stripTag($link['originNode'], $idLength);
                $link['destinationNode'] = stripTag($link['destinationNode'], $idLength);
            }
            break;
        
        case "Link":
            foreach ($rawData['nodeList'] as &$node)
            {
                $node['id'] = stripTag($node['id'], $idLength);
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
    return substr($id, 0, $length);
}
?>
