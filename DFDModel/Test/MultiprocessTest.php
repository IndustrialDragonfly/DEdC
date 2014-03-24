<?php

require_once '../Multiprocess.php';
require_once '../DataFlowDiagram.php';
require_once '../DataFlow.php';
require_once 'testDB_functions.php';

require_once 'Storage/DatabaseStorage.php';

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-11-04 at 11:16:29.
 */
class MultiprocessTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Node
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
        //$this->testDiagram->addNode($this->object);
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
     * @covers Process::__construct
     */
    public function testDidIBuild()
    {
        $this->assertTrue($this->object->getId() != NULL);
    }


}
