<?php
require_once '../DataFlowDiagram.php';
require_once '../Diagram.php';
require_once '../Entity.php';
require_once '../Element.php';
require_once '../DataFlow.php';
require_once '../Node.php';
require_once '../Process.php';
require_once '../DataStore.php';
require_once '../ExternalInteractor.php';
require_once '../Multiprocess.php';
require_once 'testDB_functions.php';
require_once 'Storage/DatabaseStorage.php';
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-03-24 at 10:44:13.
 */
class DiagramTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Diagram
     */
    protected $object;
    /**
     *
     * @var DatabaseStorage
     */
    protected $storage;

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
        $this->object = new DataFlowDiagram($this->storage);
        $this->object->save();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        //$this->object->delete();
    }

    /**
     * @covers Diagram::getNumberOfLinks
     */
//    public function testGetNumberOfLinks_empty()
//    {
//        $this->assertEquals($this->object->getNumberOfLinks(), 0);
//    }
    
    /**
     * @covers Diagram::getNumberOfLinks
     * @covers Diagram::addLink
     */
    public function testGetNumberOfLinks_smoke()
    {
        //var_dump($this->object);
        $df = new DataFlow($this->storage, $this->object->getId());
        //$df->save();
        
        //refresh from the DB
        //$this->object = new DataFlowDiagram($this->storage, $this->object->getId());
        $this->object->refresh();
        //var_dump($this->object);
        $this->assertEquals($this->object->getNumberOfLinks(), 1);
    }
    
    /**
     * @covers Diagram::getNumberOfLinks
     * @covers Diagram::addLink
     * @covers Diagram::addNode
     * @covers Diagram::getNumberOfNodes
     */
//    public function testGetNumberOfLinks_othertypes()
//    {
//        /*$ds = new DataStore($this->storage, $this->object->getId());
//        $proc = new Process($this->storage, $this->object->getId());
//        $df = new DataFlow($this->storage, $this->object->getId());
//        $df->save();
//        $ds->save();
//        $proc->save();
//        $df->setDestinationNode($ds);
//        $df->setOriginNode($proc);
//        $df->update();
//        $ds->update();
//        $proc->update();
//        $this->object->addLink($df);
//        $this->object->addNode($ds);
//        $this->object->addNode($proc);
//        $this->assertEquals($this->object->getNumberOfLinks(), 1);
//        $this->assertEquals($this->object->getNumberOfNodes(), 2);*/
//    }
//
//    /**
//     * @covers Diagram::getLinks
//     */
//    public function testGetLinks_empty()
//    {
//        $array = $this->object->getLinks();
//        $this->assertEquals(count($array), 0);
//    }
//    
//    /**
//     * @covers Diagram::getLinks
//     * @todo   Implement testGetLinks().
//     */
//    public function testGetLinks_smoke()
//    {
//        //$df = new DataFlow($this->storage, $this->object->getId());
//        
//    }
//
//    /**
//     * @covers Diagram::getLink
//     * @todo   Implement testGetLink().
//     */
//    public function testGetLink()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::addLink
//     * @todo   Implement testAddLink().
//     */
//    public function testAddLink()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::removeLink
//     * @todo   Implement testRemoveLink().
//     */
//    public function testRemoveLink()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::getNumberOfNodes
//     * @todo   Implement testGetNumberOfNodes().
//     */
//    public function testGetNumberOfNodes()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::getNodes
//     * @todo   Implement testGetNodes().
//     */
//    public function testGetNodes()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::getNode
//     * @todo   Implement testGetNode().
//     */
//    public function testGetNode()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::addNode
//     * @todo   Implement testAddNode().
//     */
//    public function testAddNode()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::removeNode
//     * @todo   Implement testRemoveNode().
//     */
//    public function testRemoveNode()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::getNumberOfDiaNodes
//     * @todo   Implement testGetNumberOfDiaNodes().
//     */
//    public function testGetNumberOfDiaNodes()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::getDiaNodes
//     * @todo   Implement testGetDiaNodes().
//     */
//    public function testGetDiaNodes()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::getDiaNode
//     * @todo   Implement testGetDiaNode().
//     */
//    public function testGetDiaNode()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::addDiaNode
//     * @todo   Implement testAddDiaNode().
//     */
//    public function testAddDiaNode()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::removeDiaNode
//     * @todo   Implement testRemoveDiaNode().
//     */
//    public function testRemoveDiaNode()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::getNumberOfAncestors
//     * @todo   Implement testGetNumberOfAncestors().
//     */
//    public function testGetNumberOfAncestors()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::getParent
//     * @todo   Implement testGetParent().
//     */
//    public function testGetParent()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::getEldestParent
//     * @todo   Implement testGetEldestParent().
//     */
//    public function testGetEldestParent()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::getNthAncestor
//     * @todo   Implement testGetNthAncestor().
//     */
//    public function testGetNthAncestor()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::getAncestry
//     * @todo   Implement testGetAncestry().
//     */
//    public function testGetAncestry()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::getParentDiaNode
//     * @todo   Implement testGetParentDiaNode().
//     */
//    public function testGetParentDiaNode()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::setParentDiaNode
//     * @todo   Implement testSetParentDiaNode().
//     */
//    public function testSetParentDiaNode()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::getAssociativeArray
//     * @todo   Implement testGetAssociativeArray().
//     */
//    public function testGetAssociativeArray()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::loadAssociativeArray
//     * @todo   Implement testLoadAssociativeArray().
//     */
//    public function testLoadAssociativeArray()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::save
//     * @todo   Implement testSave().
//     */
//    public function testSave()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::update
//     * @todo   Implement testUpdate().
//     */
//    public function testUpdate()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
//
//    /**
//     * @covers Diagram::delete
//     * @todo   Implement testDelete().
//     */
//    public function testDelete()
//    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }

}
