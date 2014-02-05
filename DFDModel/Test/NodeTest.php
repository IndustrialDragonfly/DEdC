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
        $this->storage = new DatabaseStorage();
        $this->object = new Process($this->storage);
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
      $aDF = new DataFlow($this->storage);
      $this->object->addLink($aDF);
      $this->assertEquals(1, $this->object->getNumberOfLinks());
   }

   /**
    * @covers Node::addLink
    */
   public function testAddLink_smoke()
   {
      $aDF = new DataFlow($this->storage);
      $this->object->addLink($aDF);
      $this->assertEquals(1, $this->object->getNumberOfLinks());
      $annotherDF =  $this->object->getLinkbyPosition(0);
      $this->assertEquals($aDF->getId(), $annotherDF);
   }
   
   /**
    * @covers Node::addLink
    * @expectedException BadFunctionCallException
    */
   public function testAddLink_invalidInput()
   {
      $aNode =new Process($this->storage);
      $this->object->addLink($aNode);
   }

   /**
    * @covers Node::removeLink
    */
   public function testRemoveLink_smoke()
   {
      $aDF = new DataFlow($this->storage);
      $aDF->setOriginNode($this->object);
      $aDF->save();
      $this->assertEquals(1, $this->object->getNumberOfLinks());
      // Only Links can break a linkage
      $aDF->removeNode($this->object);
      $this->assertEquals(0, $this->object->getNumberOfLinks());
      $this->assertNull($aDF->getOriginNode());
   }
   
   /**
    * @covers Node::removeLink
    * @expectedException BadFunctionCallException
    */
   public function testRemoveLink_empty()
   {
      $aDF = new DataFlow($this->storage);
      $this->object->removeLink($aDF);
   }

   /**
    * @covers Node::getLinkbyPosition
    */
   public function testGetLinkbyPosition_smoke()
   {
      $aDF = new DataFlow($this->storage);
      $aDF->setOriginNode($this->object);
      $this->assertEquals($this->object->getLinkbyPosition(0), $aDF->getId());
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
      $aDF = new DataFlow($this->storage);
      $aDF->setOriginNode($this->object);
      $this->assertEquals($aDF->getId(), $this->object->getLinkbyId($aDF->getId()));
   }
   
   /**
    * @covers Node::getLinkbyId
    * looking for something not in the list
    */
   public function testGetLinkbyId_null()
   {
      $aDF = new DataFlow($this->storage);
      $this->assertNull($this->object->getLinkbyId($aDF->getId()));
   }
   
   /**
    * @covers Node::getLinkbyId
    */
   public function testGetLinkbyId_BiggerSmoke()
   {
      $aDF = new DataFlow($this->storage);
      $aDF->setOriginNode($this->object);
      for($i = 0; $i<10; $i++)
      {
         $annotherDF = new DataFlow($this->storage);
         $annotherDF->setOriginNode($this->object);
      }
      // Ok, this might be reduntant now...
      $this->assertEquals($aDF->getId(), $this->object->getLinkbyId($aDF->getId()));
   }

   /**
    * This test is somewhat broken under the refactor, especially since
    * removeAllLinks is probably due for removal.
    * @covers Node::removeAllLinks
    */
   public function testRemoveAllLinks()
   {
      for($i = 0; $i<10; $i++)
      {
         $annotherDF = new DataFlow($this->storage);
         $annotherDF->setOriginNode($this->object);
         $annotherDF->save();
      }
      $this->assertEquals(10, $this->object->getNumberOfLinks());
      $this->object->removeAllLinks();
      $this->object->update();
//      $this->assertEquals(0, $this->object->getNumberOfLinks()); 
      // Check refreshed node for correct number of links
      $node_id = $this->object->getId();
      $this->object = new Process($this->storage, $node_id);
      $this->assertEquals(0, $this->object->getNumberOfLinks());
      
      // Refresh annotherDF
      $annotherDF_id = $annotherDF->getId();
      $annotherDF = new DataFlow($this->storage, $annotherDF_id);
      $this->assertNull($annotherDF->getOriginNode());
   }

}
