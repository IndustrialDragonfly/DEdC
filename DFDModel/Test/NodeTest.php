<?php
require_once '../Node.php';
require_once '../DataFlow.php';
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
      $this->object = new Node;
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
    * @expectedException BadFunctionCallException
    */
   public function testGetNumberOfLinks_invalidInput()
   {
      $aNode = new Node;
      $this->object->addLink($aNode);
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
    * @todo   Implement testAddLink().
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
      $aNode = new Node;
      $this->object->addLink($aNode);
   }

   /**
    * @covers Node::removeLink
    * @todo   Implement testRemoveLink().
    */
   public function testRemoveLink_smoke()
   {
      $aDF = new DataFlow;
      $aDF->setOriginNode($this->object);
      //$this->object->addLink($aDF);
      $this->assertEquals(1, $this->object->getNumberOfLinks());
      $this->assertTrue($this->object->removeLink($aDF));
      $this->assertEquals(0, $this->object->getNumberOfLinks());
   }

   /**
    * @covers Node::getLinkbyPosition
    * @todo   Implement testGetLinkbyPosition().
    */
   public function testGetLinkbyPosition()
   {
      // Remove the following lines when you implement this test.
      $this->markTestIncomplete(
              'This test has not been implemented yet.'
      );
   }

   /**
    * @covers Node::getLinkbyId
    * @todo   Implement testGetLinkbyId().
    */
   public function testGetLinkbyId()
   {
      // Remove the following lines when you implement this test.
      $this->markTestIncomplete(
              'This test has not been implemented yet.'
      );
   }

   /**
    * @covers Node::removeAllLinks
    * @todo   Implement testRemoveAllLinks().
    */
   public function testRemoveAllLinks()
   {
      // Remove the following lines when you implement this test.
      $this->markTestIncomplete(
              'This test has not been implemented yet.'
      );
   }

}
