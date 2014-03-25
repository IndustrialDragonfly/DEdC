<?php

require_once '../Entity.php';
require_once '../Process.php';
require_once '../Element.php';
require_once '../DataFlowDiagram.php';
require_once 'Storage/DatabaseStorage.php';
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-11-03 at 16:23:42.
 */
class EntityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Entity
     */
    protected $object;
    
    /**
     *
     * @var DatabaseStorage
     */
    protected $storage;
    /**
     *
     * @var Diagram
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
        $this->object = new Process($this->storage, $this->testDiagram->getId());
        $this->testDiagram->refresh();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->testDiagram->delete();
    }

    /**
     * @covers Entity::setLabel
     * @covers Entity::getLabel
     */
    public function testSetLabelgetLabel_empty()
    {
        $testStr = '';
        //$this->object->setLabel($testStr);
        $this->assertEquals($testStr, $this->object->getLabel());
    }

    /**
     * @covers Entity::setLabel
     * @covers Entity::getLabel
     */
    public function testSetLabelgetLabel_smoke()
    {
        $testStr = 'something';
        $this->object->setLabel($testStr);
        $this->assertEquals($testStr, $this->object->getLabel());
    }

    /**
     * @covers Entity::getId
     */
    public function testGetId_smoke()
    {
        $this->assertTrue($this->object->getId() != '');
    }

    /**
     * @covers Entity::getId
     */
    public function testGetId_randomnessOfId()
    {
        $aEntity = new Process($this->storage, $this->testDiagram->getId());
        $this->assertTrue($this->object->getId() != $aEntity->getId());
    }

    /**
     * @covers Entity::setOriginator
     * @covers Entity::getOriginator
     */
    public function testSetOriginatorGetOriginator_smoke()
    {
        $testStr = 'something';
        $this->object->setOriginator($testStr);
        $this->assertEquals($testStr, $this->object->getOriginator());
    }

    /**
     * @covers Entity::setOriginator
     * @covers Entity::getOriginator
     */
    public function testSetOriginatorGetOriginator_empty()
    {
        $testStr = '';
        //$this->object->setOriginator($testStr);
        $this->assertEquals($testStr, $this->object->getOriginator());
    }

    /**
     * @covers Entity::setOrganization
     * @covers Entity::getOrganization
     */
    public function testSetOrganizationgetOrganization_empty()
    {
        $testStr = '';
        //$this->object->setLabel($testStr);
        $this->assertEquals($testStr, $this->object->getOrganization());
    }

    /**
     * @covers Entity::setOrganization
     * @covers Entity::getOrganization
     */
    public function testSetOrganizationgetOrganization_smoke()
    {
        $testStr = 'something';
        $this->object->setOrganization($testStr);
        $this->assertEquals($testStr, $this->object->getOrganization());
    }
    
    /**
     * @covers Entity::loadAssocitiveArray
     * @covers Entity::getAssocitiveArray
     */
    public function testLoadAssocitiveArray_empty()
    {
        $newEntity = new Process($this->storage, $this->testDiagram->getId());
        $newEntity->loadAssociativeArray($this->object->getAssociativeArray());
        $this->assertEquals($this->object->getAssociativeArray()['label'], $newEntity->getAssociativeArray()['label']);
        $this->assertEquals($this->object->getAssociativeArray()['originator'], $newEntity->getAssociativeArray()['originator']);
        $this->assertEquals($this->object->getAssociativeArray()['organization'], $newEntity->getAssociativeArray()['organization']);
        $this->assertFalse($this->object->getAssociativeArray()['id'] == $newEntity->getAssociativeArray()['id']);
    }
    
    /**
     * @covers Entity::loadAssocitiveArray
     * @covers Entity::getAssocitiveArray
     */
    public function testLoadAssocitiveArray_smoke()
    {
        $this->object->setLabel("newLabel");
        $this->object->setOrganization("InD");
        $this->object->setOriginator("Josh");
        $newEntity = new Process($this->storage, $this->testDiagram->getId());
        $newEntity->loadAssociativeArray($this->object->getAssociativeArray());
        $this->assertEquals($this->object->getAssociativeArray()['label'], $newEntity->getAssociativeArray()['label']);
        $this->assertEquals($this->object->getAssociativeArray()['originator'], $newEntity->getAssociativeArray()['originator']);
        $this->assertEquals($this->object->getAssociativeArray()['organization'], $newEntity->getAssociativeArray()['organization']);
        $this->assertFalse($this->object->getAssociativeArray()['id'] == $newEntity->getAssociativeArray()['id']);
    }
    
    /**
     * @covers Entity::loadAssocitiveArray
     * @covers Entity::getAssocitiveArray
     */
    public function testLoadAssociativeArray_missingParameter()
    {
        $testLabel = "thingy";
        $testOrginator = "Josh";
        $testOrganization = "InD";
        //missing organization
        $assocArray1 = Array();
        $assocArray1['label'] = $testLabel;
        $assocArray1['originator'] = $testOrginator;
        //$assocArray1['organization'] = $testOrganization;
        $this->object->loadAssociativeArray($assocArray1);
        $this->assertTrue($this->object->getAssociativeArray()['label'] == $testLabel);
        $this->assertTrue($this->object->getAssociativeArray()['originator'] == $testOrginator);
        $this->assertFalse($this->object->getAssociativeArray()['organization'] == $testOrganization);
        $this->assertTrue($this->object->getAssociativeArray()['organization'] == "");
        
        //missing originator
        $assocArray2 = Array();
        $assocArray2['label'] = $testLabel;
        //$assocArray2['originator'] = $testOrginator;
        $assocArray2['organization'] = $testOrganization;
        $this->object->loadAssociativeArray($assocArray2);
        $this->assertTrue($this->object->getAssociativeArray()['label'] == $testLabel);
        $this->assertFalse($this->object->getAssociativeArray()['originator'] == $testOrginator);
        $this->assertTrue($this->object->getAssociativeArray()['organization'] == $testOrganization);
        $this->assertTrue($this->object->getAssociativeArray()['originator'] == "");
        
        
        //missing label
        $assocArray3 = Array();
        //$assocArray3['label'] = $testLabel;
        $assocArray3['originator'] = $testOrginator;
        $assocArray3['organization'] = $testOrganization;
        $this->object->loadAssociativeArray($assocArray3);
        $this->assertFalse($this->object->getAssociativeArray()['label'] == $testLabel);
        $this->assertTrue($this->object->getAssociativeArray()['originator'] == $testOrginator);
        $this->assertTrue($this->object->getAssociativeArray()['organization'] == $testOrganization);
        $this->assertTrue($this->object->getAssociativeArray()['label'] == "");
    }
    

}