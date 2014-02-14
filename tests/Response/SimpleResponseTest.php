<?php
require_once 'Response/SimpleResponse.php';
require_once 'Response/associativeArrayManager.php';

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
            "genericType" => "Diagram",
            "ancestry" => array("AB", "th65"),
            "nodeList" => array(array("id" => "aoeu654", "type" => "Process", "label" => "SomeProcess", "x" => "3", "y" => "4"), 
                array("id" => "iuoa8", "type" => "DataStore", "label" => "SomeDataStore", "x" => "8", "y" => "22")),
            "linkList" => array(array("id" => "87oeuao", "type" => "DataFlow", "label" => "SomeDF", "originNode" => "aoeu654", "destinationNode" => "iuoa8", "x" => "88", "y" => "22")),
            "DiaNodeList" => array(array("id" => "sthsrch", "type" => "Multiprocess", "label" => "SomeMP1", "x" => "55", "y" => "44"), 
                array("id" => "6548", "type" => "Multiprocess", "label" => "SomeMP2", "x" => "88", "y" => "66")),
            "diaNode" => "crgoeu"
        );
        
        $dfdArrayTag = addTags($dfdArray, "_id");
        
        $dfdJson = json_encode($dfdArrayTag);
            
        // Finally perform the actual test
        $this->object = new SimpleResponse($dfdArray);
        $resultJson = $this->object->getRepresentation();

        $this->assertEquals($dfdJson, $resultJson);
    }
}

?>
