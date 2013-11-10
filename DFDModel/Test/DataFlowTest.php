<?php
require_once '../Node.php';
require_once '../Process.php';
require_once '../DataFlow.php';
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-11-04 at 10:47:16.
 */
class DataFlowTest extends PHPUnit_Framework_TestCase
{
   /**
    * @var DataFlow
    */
   protected $object;

   /**
    * Sets up the fixture, for example, opens a network connection.
    * This method is called before a test is executed.
    */
   protected function setUp()
   {
      $this->object = new DataFlow;
   }

   /**
    * Tears down the fixture, for example, closes a network connection.
    * This method is called after a test is executed.
    */
   protected function tearDown()
   {
      
   }

   /**
    * @covers DataFlow::getOriginNode
    */
   public function testGetOriginNode_null()
   {
      $this->assertNull($this->object->getOriginNode());
   }
   
   /**
    * @covers DataFlow::getOriginNode
    * @covers DataFlow::setOriginNode
    */
   public function testGetOriginNodeSetOriginNode_smoke()
   {
      $aNode = new Process;
      $this->object->setOriginNode($aNode);
      $this->assertEquals($aNode, $this->object->getOriginNode());
      $this->assertEquals($this->object, $aNode->getLinkbyId($this->object->getId()));
   }
   
   /**
    * @covers DataFlow::setOriginNode
    * @expectedException BadFunctionCallException
    */
   public function testSetOriginNode_null()
   {
      $this->object->setOriginNode(null);
   }
   
   /**
    * @covers DataFlow::setOriginNode
    * @expectedException BadFunctionCallException
    */
   public function testSetOriginNode_invalidInput()
   {
      $aDF = new DataFlow;
      $this->object->setOriginNode($aDF);
   }

   /**
    * @covers DataFlow::clearOriginNode.
    */
   public function testClearOriginNode_null()
   {
      $this->assertNull($this->object->getOriginNode());
      $this->object->clearOriginNode();
      $this->assertNull($this->object->getOriginNode());
   }
   
   /**
    * @covers DataFlow::clearOriginNode.
    */
   public function testClearOriginNode_smoke()
   {
      $this->assertNull($this->object->getOriginNode());
      $aNode = new Process;
      $this->object->setOriginNode($aNode);
      $this->assertEquals($this->object, $aNode->getLinkbyId($this->object->getId()));
      $this->object->clearOriginNode();
      $this->assertNull($this->object->getOriginNode());
      $this->assertNull( $aNode->getLinkbyId($this->object->getId()));
   }
   
   /**
    * @covers DataFlow::getDestinationNode
    */
   public function testGetDestinationNode_null()
   {
      $this->assertNull($this->object->getDestinationNode());
   }
   
   /**
    * @covers DataFlow::getDestinationNode
    * @covers DataFlow::setDestinationNode
    */
   public function testGetDestinationNodeSetDestinationNode_smoke()
   {
      $aNode =new Process;
      $this->object->setDestinationNode($aNode);
      $this->assertEquals($aNode, $this->object->getDestinationNode());
      $this->assertEquals($this->object, $aNode->getLinkbyId($this->object->getId()));
   }
   
   /**
    * @covers DataFlow::setDestinationNode
    * @expectedException BadFunctionCallException
    */
   public function testSetDestinationNode_null()
   {
      $this->object->setDestinationNode(null);
   }
   
   /**
    * @covers DataFlow::setDestinationNode
    * @expectedException BadFunctionCallException
    */
   public function testSetDestinationNode_invalidInput()
   {
      $aDF = new DataFlow;
      $this->object->setDestinationNode($aDF);
   }

   /**
    * @covers DataFlow::clearDestinationNode.
    */
   public function testClearDestinationNode_null()
   {
      $this->assertNull($this->object->getDestinationNode());
      $this->object->clearDestinationNode();
      $this->assertNull($this->object->getDestinationNode());
   }
   
   /**
    * @covers DataFlow::clearDestinationNode.
    */
   public function testClearDestinationNode_smoke()
   {
      $this->assertNull($this->object->getDestinationNode());
      $aNode =new Process;
      $this->object->setDestinationNode($aNode);
      $this->assertEquals($this->object, $aNode->getLinkbyId($this->object->getId()));
      $this->object->clearDestinationNode();
      $this->assertNull($this->object->getDestinationNode());
      $this->assertNull( $aNode->getLinkbyId($this->object->getId()));
   }

   /**
    * @covers DataFlow::removeAllLinks
    */
   public function testRemoveAllLinks_null()
   {
      $this->object->removeAllLinks();
      $this->assertNull($this->object->getOriginNode());
      $this->assertNull($this->object->getDestinationNode());
   }
   
   /**
    * @covers DataFlow::removeAllLinks
    */
   public function testRemoveAllLinks_smoke()
   {
      $node1 =new Process;
      $node2 =new Process;
      $this->object->setOriginNode($node1);
      $this->object->setDestinationNode($node2);
      $this->object->removeAllLinks();
      $this->assertNull($this->object->getOriginNode());
      $this->assertNull($this->object->getDestinationNode());
      $this->assertNull($node1->getLinkbyId($this->object->getId()));
      $this->assertNull($node2->getLinkbyId($this->object->getId()));
   }
   
   /**
    * @covers DataFlow::save
    */
   public function testSave()
   {
      $node = new Process;
      $node->setLabel('someNode');
      $node->setLocation(20, 20);
      $node->setOriginator('Josh');
      $this->object->setLabel('name');
      $this->object->setOriginator('Josh');
      $this->object->setLocation(50, 50);
      //$this->object->setOriginNode($node);
      //$this->object->setDestinationNode($node);
      
      
      $pdo = $this->getConnection();
      //$node->save($pdo);
      $this->object->save($pdo);
      
      //$this->resetDB($pdo);
   }
   
   /**
    * 
    * @return PDO
    */
   public function getConnection()
   {
      $db_hostname = 'localhost';
      $db_database = 'dedc';
      $db_username = 'tester';
      $db_password = 'test';

      // Combined driver/host/db string
      // Comment and uncomment the relevant ones for your prefered RDMS
      $db_id = "mysql:host=$db_hostname;dbname=$db_database";
      //$db_id = "pgsql:host=$db_hostname;dbname=$db_database";

      // DB Setup
      $dbh;
      try 
      {
          $dbh = new PDO($db_id, $db_username, $db_password);
      }
      catch (PDOException $e)
      {
          die("Failed to connect to DB" . $e->getMessage());
      }
      $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
      return $dbh;
   }
   
   public function resetDB($pdo)
   {
      if( $pdo instanceof PDO)
      {
         $pdo->query('USE dedc;');
         $pdo->query('SET foreign_key_checks=0;');
         $pdo->query('TRUNCATE TABLE entity;');
         $pdo->query('TRUNCATE TABLE element;');
         $pdo->query('TRUNCATE TABLE dataflow;');
         $pdo->query('TRUNCATE TABLE external_links;');
         $pdo->query('TRUNCATE TABLE element_list;');
         $pdo->query('TRUNCATE TABLE multiprocess;');
         $pdo->query('SET foreign_key_checks=1;');
      }
      else 
      {
         throw new BadFunctionCallException("input parameter was not a PDO");
      }
   }

}
