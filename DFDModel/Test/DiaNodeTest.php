<?php

require_once '../Multiprocess.php';
require_once '../Node.php';
require_once '../Process.php';
require_once '../DataFlow.php';
require_once '../DiaNode.php';
require_once 'testDB_functions.php';

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-11-04 at 13:04:04.
 */
class DiaNodeTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var DiaNode
     */
    protected $object;

    /**
     *
     * @var DatabaseStorage
     */
    protected $storage;
    
    /**
     *
     * @var DataFlowDiagram
     */
    protected $testDiagram;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if ($this->storage == null)
        {
            $this->storage = new DatabaseStorage();
        }
        
        $this->testDiagram = new DataFlowDiagram($this->storage);
        $this->testDiagram->save();
        $this->object = new Multiprocess($this->storage, $this->testDiagram->getId());
        $this->object->save();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        //clear the DB
        $this->testDiagram->delete();
    }

    /**
     * @covers Multiprocess::getSubDFD
     */
    public function testGetSubDiagram_null()
    {
        $this->assertTrue($this->object->getSubDiagram() == NULL);
    }

    /**
     * @covers Multiprocess::setSubDFD
     * @covers Multiprocess::getSubDFD
     */
    public function testSetSubDiagram_smoke()
    {
        $this->object->setSubDiagram($this->testDiagram->getId());
        $this->assertTrue($this->object->getSubDiagram() == $this->testDiagram->getId());
        
    }

    /**
     * @covers Multiprocess::setSubDFD
     * @covers Multiprocess::getSubDFD
     * @expectedException BadFunctionCallException
     */
    public function testSetSubDiagram_invalidInput()
    {
        $element = new Process;
        $this->object->setSubDFD($element);
    }
    
    public function testLoadAssociativeArray_empty()
    {
        $assocArray = $this->object->getAssociativeArray();
        $annotherMP = new Multiprocess($this->storage, $assocArray);
        $this->assertEquals($this->object->getAssociativeArray()['childDiagramId'], $annotherMP->getAssociativeArray()['childDiagramId']);
    }
    
    public function testLoadAssociativeArray_smoke()
    {
        $testLabel = "thingy";
        $testOrginator = "Josh";
        $testOrganization = "InD";
        
        $testX = 50;
        $testY = 150;
        $testParentID = $this->testDiagram->getId();
        
        $assocArray['label'] = $testLabel;
        $assocArray['originator'] = $testOrginator;
        $assocArray['organization'] = $testOrganization;
        $assocArray['x'] = $testX;
        $assocArray['y'] = $testY;
        $assocArray['diagramId'] = $testParentID;
        $this->object->loadAssociativeArray($assocArray);
        $this->object->update();
        
        $testSubDiagram = new DataFlowDiagram($this->storage, $this->object->getId());
        $testSubDiagram->save();
        $this->object->setSubDiagram($testSubDiagram->getId());
        $this->assertEquals($this->object->getAssociativeArray()['childDiagramId'], $testSubDiagram->getId());
        
    }
    
    public function testLoadAssociativeArray_missingParameter()
    {
        $testLabel = "thingy";
        $testOrginator = "Josh";
        $testOrganization = "InD";
        
        $testX = 50;
        $testY = 150;
        $testParentID = $this->testDiagram->getId();
        
        $assocArray['label'] = $testLabel;
        $assocArray['originator'] = $testOrginator;
        $assocArray['organization'] = $testOrganization;
        $assocArray['x'] = $testX;
        $assocArray['y'] = $testY;
        $assocArray['diagramId'] = $testParentID;
        $this->object->loadAssociativeArray($assocArray);
        $this->object->update();
        
        $this->assertNull($this->object->getAssociativeArray()['childDiagramId']);
    }

}
