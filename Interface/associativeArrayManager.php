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
            $rawData['id'] = $rawData['id']->getTaggedId();
            foreach ($rawData['nodeList'] as &$node)
            {
                $node['id'] = $node['id']->getTaggedId();
            }

            foreach ($rawData['linkList'] as &$link)
            {
                $link['id'] = $link['id']->getTaggedId();
                $link['originNode']['id'] = $link['originNode']['id']->getTaggedId();
                $link['destinationNode']['id'] = $link['destinationNode']['id']->getTaggedId();
            }

            foreach ($rawData['DiaNodeList'] as &$diaNode)
            {
                $diaNode['id'] = $diaNode['id']->getTaggedId();
            }

            for ($i = 0; $i < count($rawData['ancestry']); $i++)
            {
                $rawData['ancestry'][$i] = $rawData['ancestry'][$i]->getTaggedId();
            }

            if (isset($rawData['diaNode']))
            {
                $rawData['diaNode'] = $rawData['diaNode']->getTaggedId();
            }
            break;
           
        case "diaNode":
            // Intentionally let it fall through to Node so that we only
            // have to write linkList code here once
        case "Node":
            $rawData['id'] = $rawData['id']->getTaggedId();
            $rawData['diagramId'] = $rawData['diagramId']->getTaggedId();
            foreach ($rawData['linkList'] as &$link)
            {
                $link['id'] = $link['id']->getTaggedId();
                $link['originNode']['id'] = $link['originNode']['id']->getTaggedId();
                $link['destinationNode']['id'] = $link['destinationNode']['id']->getTaggedId();
            }
            break;
        
        case "Link":
            $rawData['id'] = $rawData['id']->getTaggedId();
            $rawData['diagramId'] = $rawData['diagramId']->getTaggedId();
            $rawData['originNode']['id'] = $rawData['originNode']['id']->getTaggedId();
            $rawData['destinationNode']['id'] = $rawData['destinationNode']['id']->getTaggedId();
            break;
            
        case "List":
            for ($i = 0; $i < count($rawData['list']); $i++)
            {
                $rawData['list'][$i]['id'] = $rawData['list'][$i]['id']->getTaggedId();
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
    // One thing that always holds true is it has an id, so set it here
    $rawData['id'] = new ID($rawData['id']);
    
    switch($rawData['genericType'])
    {
        case "Diagram":
            foreach ($rawData['nodeList'] as &$node)
            {
                $node['id'] = new ID($node['id']);
            }

            foreach ($rawData['linkList'] as &$link)
            {
                $link['id'] = new ID($link['id']);
                $link['originNode']['id'] = new ID($link['originNode']['id']);
                $link['destinationNode']['id'] = new ID($link['destinationNode']['id']);
            }

            foreach ($rawData['DiaNodeList'] as &$diaNode)
            {
                $diaNode['id'] = new ID($diaNode['id']);
                $diaNode['childDiagramId'] = new ID($diaNode['childDiagramId']['id']);
            }

            for ($i = 0; $i < count($rawData['ancestry']); $i++)
            {
                $rawData['ancestry'][$i] = new ID($rawData['ancestry'][$i]);
            }

            if (isset($rawData['diaNode']))
            {
                $rawData['diaNode'] = new ID($rawData['diaNode']);
            }
            break;
           
        case "diaNode":
            // Intentionally let it fall through to Node so that we only
            // have to write linkList code here once
            $rawData['childDiagramId'] = new ID($rawData['childDiagramId']);
        case "Node":
            $rawData['diagramId'] = new ID($rawData['diagramId']);
            foreach ($rawData['linkList'] as &$link)
            {
                $link['id'] = new ID($link['id']);
                $link['originNode']['id'] = new ID($link['originNode']['id']);
                $link['destinationNode']['id'] = new ID($link['destinationNode']['id']);
            }
            break;
        
        case "Link":
            $rawData['diagramId'] = new ID($rawData['diagramId']);
            $rawData['originNode']['id'] = new ID($rawData['originNode']['id']);
            $rawData['destinationNode']['id'] = new ID($rawData['destinationNode']['id']);
            break;
        
        default:
            // Probably should update to a specialized exception
            throw new BadFunctionCallException("Not a valid genericType");
            break;
        
    }
    
    return $rawData;
}
?>
