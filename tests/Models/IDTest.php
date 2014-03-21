<?php
require_once 'Models/ID.php';

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-03-21 at 19:16:44.
 */
class IDTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider idProvider
     */
    public function testConstructorWithoutTag($id)
    {
        $idObject = new ID($id);
        $this->assertEquals($id, $idObject->getId());
    }

    /**
     * @dataProvider idProvider
     */
    public function testConstructorWithTag($id)
    {
        $idObject = new ID($id . "_id");
        $this->assertEquals($id, $idObject->getId());
    }
    
    /**
     * Test ID::getTaggedId
     * @dataProvider idProvider
     */
    public function testGetTaggedId($id)
    {
        $idObject = new ID($id);
        $this->assertEquals($id . "_id", $idObject->getTaggedId());
    }
    
    /**
     * Test generation of IDs
     */
    public function testGenerateId()
    {
        $idObject = new ID();
        $this->assertTrue(is_string($idObject->getId()));
        $this->assertTrue(is_string($idObject->getTaggedId()));
    }
    
    /**
     * Provides IDs
     */
    public function idProvider()
    {
        return array(
            array("asdf"),
            array("1234"),
            array(""),
            array(NULL)
        );
    }

}
