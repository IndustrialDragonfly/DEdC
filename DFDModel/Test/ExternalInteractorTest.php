<?php
require_once '../ExternalInteractor.php';
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-11-04 at 11:27:08.
 */
class ExternalInteractorTest extends PHPUnit_Framework_TestCase
{
   /**
    * @var ExternalInteractor
    */
   protected $object;

   /**
    * Sets up the fixture, for example, opens a network connection.
    * This method is called before a test is executed.
    */
   protected function setUp()
   {
      $this->object = new ExternalInteractor;
   }

   /**
    * Tears down the fixture, for example, closes a network connection.
    * This method is called after a test is executed.
    */
   protected function tearDown()
   {
      
   }
   
   /**
    * @covers ExternalInteractor::__construct
    */
   public function testDidIBuild()
   {
      $this->assertTrue($this->object->getId() != NULL);
   }

}
