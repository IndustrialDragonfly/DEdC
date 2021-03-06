<?php
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-11-04 at 11:37:41.
 */
class DataFlowDiagramTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var DataFlowDiagram
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
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        //clear the DB
        $this->object->refresh();
        $this->object->delete();
    }

    /**
     * @covers Process::__construct
     */
    public function testDidIBuild()
    {
        $this->assertTrue($this->object->getId() != NULL);
    }
}
