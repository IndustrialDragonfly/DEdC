<?php
require_once 'Response/SimpleResponse.php';

/**
 * Tests on the SimpleResponse class
 *
 * @author eugene
 */

class SimpleResponseTest extends PHPUnit_Framework_TestCase
{
        /**
     * @var simpleRequest
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        // Requires input
        //$this->object = new SimpleResponse;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     * @covers createRepresentation, __construct()
     */
    protected function tearDown()
    {
        
    }
    
    public function testCreateRepresentationDFD()
    {
        // Setup an array to simulate a DFD from the data model
        $dfdArray = array(
            "id" => "aoeua54aoeu54",
            "label" => "SomeDFD",
            "originator" => "The Eugene",
            "organization" => "DEdC",
            "type" => "DataFlowDiagram",
            "genericType" => "DataFlowDiagram",
            "ancestry" => array("AB", "th65"),
            "nodeList" => array(array("id" => "aoeu654", "type" => "Process", "label" => "SomeProcess", "x" => "3", "y" => "4"), 
                array("id" => "iuoa8", "type" => "DataStore", "label" => "SomeDataStore", "x" => "8", "y" => "22")),
            "linkList" => array(array("id" => "87oeuao", "type" => "DataFlow", "label" => "SomeDF", "originNode" => "aoeu654", "destinationNode" => "iuoa8", "x" => "88", "y" => "22")),
            "subDFDNodeList" => array(array("id" => "sthsrch", "type" => "Multiprocess", "label" => "SomeMP1", "x" => "55", "y" => "44"), 
                array("id" => "6548", "type" => "Multiprocess", "label" => "SomeMP2", "x" => "88", "y" => "66")),
            "subDFDNode" => "crgoeu"
        );
        
        // Setup string to compare output to
        $dfdJson =<<<EOT
{
    "id": "{$dfdArray['id']}_id",
    "label": "{$dfdArray['label']}",
    "type": "{$dfdArray['type']}",
    "originator": "{$dfdArray['originator']}",
    "genericType": "{$dfdArray['genericType']}",
    "nodes": 
    [
        {
            "id": "{$dfdArray['nodeList'][0]['id']}_id",
            "type": "{$dfdArray['nodeList'][0]['type']}",
            "label": "{$dfdArray['nodeList'][0]['label']}",
            "x": "{$dfdArray['nodeList'][0]['x']}",
            "y": "{$dfdArray['nodeList'][0]['y']}"
        },
        {
            "id": "{$dfdArray['nodeList'][1]['id']}_id",
            "type": "{$dfdArray['nodeList'][1]['type']}",
            "label": "{$dfdArray['nodeList'][1]['label']}",
            "x": "{$dfdArray['nodeList'][1]['x']}",
            "y": "{$dfdArray['nodeList'][1]['y']}"
        }
    ],
    "links": 
        [
            {
                "id": "{$dfdArray['linkList'][0]['id']}_id",
                "type": "{$dfdArray['linkList'][0]['type']}",
                "label": "{$dfdArray['linkList'][0]['label']}",
                "origin_id": "{$dfdArray['linkList'][0]['originNode']}_id",
                "dest_id": "{$dfdArray['linkList'][0]['destinationNode']}_id",
                "x": "{$dfdArray['linkList'][0]['x']}",
                "y": "{$dfdArray['linkList'][0]['y']}"
            }
        ],
    "subDFDNodes": 
    [
        {
            "id": "{$dfdArray['subDFDNodeList'][0]['id']}_id",
            "type": "{$dfdArray['subDFDNodeList'][0]['type']}",
            "label": "{$dfdArray['subDFDNodeList'][0]['label']}",
            "x": "{$dfdArray['subDFDNodeList'][0]['x']}",
            "y": "{$dfdArray['subDFDNodeList'][0]['y']}"
        },
        {
            "id": "{$dfdArray['subDFDNodeList'][1]['id']}_id",
            "type": "{$dfdArray['subDFDNodeList'][1]['type']}",
            "label": "{$dfdArray['subDFDNodeList'][1]['label']}",
            "x": "{$dfdArray['subDFDNodeList'][1]['x']}",
            "y": "{$dfdArray['subDFDNodeList'][1]['y']}"
        }
    ]
}
EOT;
    // Finally perform the actual test
    $this->object = new SimpleResponse($dfdArray);
    $resultJson = $this->object->getRepresentation();
    
    $this->assertEquals($dfdJson, $resultJson);
    }
}

?>
