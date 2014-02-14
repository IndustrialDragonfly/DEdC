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
    // One thing that always holds true is it has an id, so set it here
    $rawData['id'] = $rawData['id'] . "_id";
    
    switch($rawData['genericType'])
    {
        case "Diagram":
            foreach ($rawData['nodeList'] as &$node)
            {
                $node['id'] = $node['id'] . "_id";
            }

            foreach ($rawData['linkList'] as &$link)
            {
                $link['id'] = $link['id'] . "_id";
                $link['originNode'] = $link['originNode'] . "_id";
                $link['destinationNode'] = $link['destinationNode'] . "_id";
            }

            foreach ($rawData['DiaNodeList'] as &$diaNode)
            {
                $diaNode['id'] = $diaNode['id'] . "_id";
            }

            for ($i = 0; $i < count($rawData['ancestry']); $i++)
            {
                $rawData['ancestry'][$i] = $rawData['ancestry'][$i] . "_id";
            }

            $rawData['diaNode'] = $rawData['diaNode'] . "_id";
            break;
           
        case "diaNode":
            $rawData['diagramId'] = $rawData['diagramId'] . "_id";
            // Intentionally let it fall through to Node so that we only
            // have to write linkList code here once
        case "Node":
            foreach ($rawData['linkList'] as &$link)
            {
                $link['id'] = $link['id'] . "_id";
                $link['originNode'] = $link['originNode'] . "_id";
                $link['destinationNode'] = $link['destinationNode'] . "_id";
            }
            break;
        
        case "Link":
            foreach ($rawData['nodeList'] as &$node)
            {
                $node['id'] = $node['id'] . "_id";
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
    return $rawData;
}
?>
