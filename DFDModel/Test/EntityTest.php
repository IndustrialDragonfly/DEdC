<?php
require_once '../Entity.php';
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
      $this->object = new Entity;
   }

   /**
    * Tears down the fixture, for example, closes a network connection.
    * This method is called after a test is executed.
    */
   protected function tearDown()
   {
      
   }

   /**
    * @covers Entity::setLabel and Entity::getLabel
    */
   public function test_Set_get_Label_empty()
   {
      $testStr = '';
      //$this->object->setLabel($testStr);
      $this->assertEquals($testStr, $this->object->getLabel());
   }
   
   /**
    * @covers Entity::setLabel and Entity::getLabel
    */
   public function test_Set_get_Label_smoke()
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
      //echo $this->object->getId();
   }
   
   /**
    * @covers Entity::getId
    */
   public function testGetId_randomnessOfId()
   {
      $aEntity = new Entity;
      $this->assertTrue( $this->object->getId() != $aEntity->getId());
   }

   /**
    * @covers Entity::setOwner
    * @todo   Implement testSetOwner().
    */
   public function testSet_Get_Owner_smoke()
   {
      $testStr = 'something';
      $this->object->setOwner($testStr);
      $this->assertEquals($testStr, $this->object->getOwner());
   }
   
   /**
    * @covers Entity::setOwner
    * @todo   Implement testSetOwner().
    */
   public function testSet_Get_Owner_empty()
   {
      $testStr = '';
      //$this->object->setOwner($testStr);
      $this->assertEquals($testStr, $this->object->getOwner());
   }

}