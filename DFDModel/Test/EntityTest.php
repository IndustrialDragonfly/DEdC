<?php
require_once '../Entity.php';
require_once '../Process.php';
require_once '../Element.php';

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
    * Sets up the fixture, for example, opens a network connection.
    * This method is called before a test is executed.
    */
   protected function setUp()
   {
      $this->object = new realizedEntity();
   }

   /**
    * Tears down the fixture, for example, closes a network connection.
    * This method is called after a test is executed.
    */
   protected function tearDown()
   {
      
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
      $this->assertTrue( $this->object->getId() != '');
   }
   
   /**
    * @covers Entity::getId
    */
   public function testGetId_randomnessOfId()
   {
      $aEntity = new realizedEntity;
      $this->assertTrue( $this->object->getId() != $aEntity->getId());
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

}


class realizedEntity extends Entity
{
    
}
