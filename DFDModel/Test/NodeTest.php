<?php
require_once '../Node.php';
require_once '../DataFlow.php';
require_once '../Process.php';
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-11-03 at 20:06:32.
 */
class NodeTest extends PHPUnit_Framework_TestCase
{
   /**
    * @var Node
    */
   protected $object;

   /**
    * Sets up the fixture, for example, opens a network connection.
    * This method is called before a test is executed.
    */
   protected function setUp()
   {
      $this->object = new Process;
   }

   /**
    * Tears down the fixture, for example, closes a network connection.
    * This method is called after a test is executed.
    */
   protected function tearDown()
   {
      
   }

   /**
    * @covers Node::getNumberOfLinks
    */
   public function testGetNumberOfLinks_empty()
   {
      $this->assertEquals(0, $this->object->getNumberOfLinks());
   }
   
   /**
    * @covers Node::getNumberOfLinks
    */
   public function testGetNumberOfLinks_smoke()
   {
      $aDF = new DataFlow;
      $this->object->addLink($aDF);
      $this->assertEquals(1, $this->object->getNumberOfLinks());
   }

   /**
    * @covers Node::addLink
    */
   public function testAddLink_smoke()
   {
      $aDF = new DataFlow;
      $this->object->addLink($aDF);
      $this->assertEquals(1, $this->object->getNumberOfLinks());
      $annotherDF =  $this->object->getLinkbyPosition(0);
      $this->assertEquals($aDF, $annotherDF);
   }
   
   /**
    * @covers Node::addLink
    * @expectedException BadFunctionCallException
    */
   public function testAddLink_invalidInput()
   {
      $aNode =new Process;
      $this->object->addLink($aNode);
   }

   /**
    * @covers Node::removeLink
    */
   public function testRemoveLink_smoke()
   {
      $aDF = new DataFlow;
      $aDF->setOriginNode($this->object);
      $this->assertEquals(1, $this->object->getNumberOfLinks());
      $this->assertTrue($this->object->removeLink($aDF));
      $this->assertEquals(0, $this->object->getNumberOfLinks());
      $this->assertNull($aDF->getOriginNode());
   }
   
   /**
    * @covers Node::removeLink
    */
   public function testRemoveLink_empty()
   {
      $aDF = new DataFlow;
      $this->assertFalse($this->object->removeLink($aDF));
   }

   /**
    * @covers Node::getLinkbyPosition
    */
   public function testGetLinkbyPosition_smoke()
   {
      $aDF = new DataFlow;
      $aDF->setOriginNode($this->object);
      $this->assertEquals($this->object->getLinkbyPosition(0), $aDF);
   }
   
   /**
    * @covers Node::getLinkbyPosition
    * @expectedException BadFunctionCallException
    */
   public function testGetLinkbyPosition_overrun()
   {
      $this->object->getLinkbyPosition(0);
   }
   
   /**
    * @covers Node::getLinkbyPosition
    * @expectedException BadFunctionCallException
    */
   public function testGetLinkbyPosition_negativeOverrun()
   {
      $this->object->getLinkbyPosition(-1);
   }

   /**
    * @covers Node::getLinkbyId
    */
   public function testGetLinkbyId_smoke()
   {
      $aDF = new DataFlow;
      $aDF->setOriginNode($this->object);
      $this->assertEquals($aDF, $this->object->getLinkbyId($aDF->getId()));
   }
   
   /**
    * @covers Node::getLinkbyId
    * looking for something not in the list
    */
   public function testGetLinkbyId_null()
   {
      $aDF = new DataFlow;
      $this->assertNull($this->object->getLinkbyId($aDF->getId()));
   }
   
   /**
    * @covers Node::getLinkbyId
    */
   public function testGetLinkbyId_BiggerSmoke()
   {
      $aDF = new DataFlow;
      $aDF->setOriginNode($this->object);
      for($i = 0; $i<10; $i++)
      {
         $annotherDF = new DataFlow;
         $annotherDF->setOriginNode($this->object);
      }
      $this->assertEquals($aDF, $this->object->getLinkbyId($aDF->getId()));
   }

   /**
    * @covers Node::removeAllLinks
    */
   public function testRemoveAllLinks()
   {
      for($i = 0; $i<10; $i++)
      {
         $annotherDF = new DataFlow;
         $annotherDF->setOriginNode($this->object);
      }
      $this->assertEquals(10, $this->object->getNumberOfLinks());
      $this->object->removeAllLinks();
      $this->assertEquals(0, $this->object->getNumberOfLinks());
      $this->assertNull($annotherDF->getOriginNode());
   }

}
