<?php
require_once "Response.php";
/**
 * Simple class to allow for testing of the abstract Request object.
 *
 * @author eugene
 */

final class SimpleResponse extends Response implements GETResponsable
{
    /**
     * Contains the raw (associative array) data for the body.
     * @var String
     */
    private $rawData;
    
    /**
     * Contains the SimpleMediaType representation of the object being sent
     * @var String
     */
    private $representation;
    
    /**
     * Constructs a SimpleMediaType response, with the input consisting of
     * an associative array that contains the information for an entity of the
     * data model
     * @param Mixed[] $data
     */
    public function __construct($data)
    {
        $this->setRawData($data);
        $this->createRepresentation();
    }
    
    /**
     * Sets the raw (associative array) data from the data model
     * 
     * @param Mixed[] $data
     */
    public function setRawData($data)
    {
        $this->rawData = $data;
    }
    
    /**
     * Converts the raw data into the SimpleMediaType representation.
     * Just switches between templates for different objects, hopefully
     * more sophisticated media types can be created in a cleaner way.
     */
    private function createRepresentation()
    {
        switch($this->rawData['genericType'])
        {
            case "DataFlowDiagram":
                // Parse and setup nodes list
                $nodeList = array();
                foreach ($this->rawData['nodeList'] as $node)
                {
                    // Using a heredoc - ugly but easy to understand
                    $nodeJson =<<<EOT
                                   {
                                        "id": "{$node['id']}_id",
                                        "type": "{$node['type']}",
                                        "label": "{$node['label']}",
                                        "x": "{$node['x']}",
                                        "y": "{$node['y']}"
                                    }
EOT;
                    
                    array_push($nodeList, $nodeJson);
                }
                // Convert nodes into a comma delimited string
                $nodeList = implode(", ", $nodeList);
                
                $linkList = array();
                foreach ($this->rawData['linkList'] as $link)
                {
                    // Using a heredoc - ugly but easy to understand
                    $linkJson =<<<EOT
                                   {
                                        "id": "{$link['id']}_id",
                                        "type": "{$link['type']}",
                                        "label": "{$link['label']}",
                                        "origin_id": {$link['originNode']},
                                        "dest_id": {$link['destinationNode']},
                                        "x": "{$link['x']}",
                                        "y": "{$link['y']}"
                                    }
EOT;
                    
                    array_push($linkList, $linkJson);
                }
                // Convert nodes into a comma delimited string
                $linkList = implode(", ", $linkList);
                
                
                // Parse and setup subDFDNodes list
                $subDFDNodeList = array();
                foreach ($this->rawData['subDFDNodeList'] as $subDFDnode)
                {
                    // Using a heredoc - ugly but easy to understand
                    $subDFDNodeJson =<<<EOT
                                   {
                                        "id": "{$subDFDnode['id']}_id",
                                        "type": "{$subDFDnode['type']}",
                                        "label": "{$subDFDnode['label']}",
                                        "x": "{$subDFDnode['x']}",
                                        "y": "{$subDFDnode['y']}"
                                    }
EOT;
                    
                    array_push($subDFDNodeList, $subDFDNodeJson);
                }
                $subDFDnodes = implode(", ", $subDFDNodeList);
                    
                $this->representation =<<<EOT
                {
                    "id": "{$this->rawData['id']}_id",
                    "label": "{$this->rawData['label']}",
                    "type": "{$this->rawData['type']}",
                    "genericType": "{$this->rawData['genericType']}",
                    "originator": "{$this->rawData['originator']}",
                    "nodes": 
                    [
                        {$nodeList}
                    ],
                    "links": 
                        [
                            $linkList
                        ],
                    "subDFDNodes": 
                    [
                        {$subDFDNodeList}
                    ]
                }
EOT;
                 break;
            case "Node":
                // Convert the linkList array into a string
                $links = implode(", ", $this->rawData['linkList']);
                $this->representation = <<<EOT
                    {
                        "id": "{$this->rawData['id']}_id",
                        "type": "{$this->rawData['type']}",
                        "genericType": "{$this->rawData['genericType']}",
                        "label": "{$this->rawData['label']}",
                        "x": "{$this->rawData['x']}",
                        "y": "{$this->rawData['y']}",
                        "originator": "{$this->rawData['originator']}",
                        "links": [$links]
                    }
EOT;
                break;
            case "SubDFDNode":
                // Convert the linkList array into a string
                $links = implode(", ", $this->rawData['linkList']);
                $this->representation = <<<EOT
                    {
                        "id": "{$this->rawData['id']}_id",
                        "type": "{$this->rawData['type']}",
                        "genericType": "{$this->rawData['genericType']}",
                        "label": "{$this->rawData['label']}",
                        "x": "{$this->rawData['x']}",
                        "y": "{$this->rawData['y']}",
                        "originator": "{$this->rawData['originator']}",
                        "links": [$links]
                    }
EOT;
                break;
            case "Link":
                $this->representation = <<<EOT
                    {
                        "id": "{$this->rawData['id']}_id",
                        "type": "{$this->rawData['type']}",
                        "genericType": "{$this->rawData['genericType']}",
                        "origin_id": "{$this->rawData['originNode']}",
                        "dest_id": "{$this->rawData['destinationNode']}",
                        "originator": "{$this->rawData['originator']}",
                        "x": "{$this->rawData['x']}",
                        "y": "{$this->rawData['y']}"
                    }
EOT;
        }
    }
    
    /**
     * Returns the SimpleMediaType representation of the data to send to the 
     * client.
     * 
     * @return String
     */
    public function getRepresentation()
    {
        return $this->representation;
    }
}
