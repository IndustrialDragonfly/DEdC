<?php
require_once '../Multiprocess.php';
require_once '../Node.php';
require_once '../DataFlow.php';
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-11-04 at 13:04:04.
 */
class MultiprocessTest extends PHPUnit_Framework_TestCase
{
   /**
    * @var Multiprocess
    */
   protected $object;

   /**
    * Sets up the fixture, for example, opens a network connection.
    * This method is called before a test is executed.
    */
   protected function setUp()
   {
      $this->object = new Multiprocess;
   }

   /**
    * Tears down the fixture, for example, closes a network connection.
    * This method is called after a test is executed.
    */
   protected function tearDown()
   {
      
   }

   /**
    * @covers Multiprocess::getSubDFD
    */
   public function testGetSubDFD_null()
   {
      $this->assertTrue($this->object->getSubDFD() != NULL);
   }

   /**
    * @covers Multiprocess::setSubDFD
    * @covers Multiprocess::getSubDFD
    */
   public function testSetSubDFD_GetSubDFD_smoke()
   {
      $dfd = new DataFlowDiagram;
      $oldDFD = $this->object->getSubDFD();
      $this->object->setSubDFD($dfd);
      $this->assertTrue($this->object->getSubDFD() != $oldDFD);
   }
   
   /**
    * @covers Multiprocess::setSubDFD
    * @covers Multiprocess::getSubDFD
    * @expectedException BadFunctionCallException
    */
   public function testSetSubDFD_GetSubDFD_invalidInput()
   {
      $element = new Element;
      $this->object->setSubDFD($element);
   }

   /**
    * @covers Node::addLink
    */
   public function testAddLink_smoke()
   {
      $aDF = new DataFlow;
      $this->object->addLink($aDF);
      $this->assertEquals(1, $this->object->getNumberOfLinks());
      $this->assertEquals(1, $this->object->getSubDFD()->getNumberOfExternalLinks());
      $annotherDF =  $this->object->getLinkbyPosition(0);
      $this->assertEquals($aDF, $annotherDF);
      
      $this->assertEquals(
         $this->object->getSubDFD()->getExternalLinkById($aDF->getId()),
         $aDF);
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
    */
   public function testRemoveLink_smoke()
   {
      $aDF = new DataFlow;
      $aDF->setOriginNode($this->object);
      $this->assertEquals(1, $this->object->getNumberOfLinks());
      $this->assertEquals(1, $this->object->getSubDFD()->getNumberOfExternalLinks());
      $this->assertTrue($this->object->removeLink($aDF));
      $this->assertEquals(0, $this->object->getNumberOfLinks());
      $this->assertEquals(0, $this->object->getSubDFD()->getNumberOfExternalLinks());
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

}
