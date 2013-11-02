<?php
include_once 'Entity.php';

/**
 * Description of Element
 *
 * @author Josh Clark
 */
class Element extends Entity
{
   //<editor-fold desc="Attributes" defaultstate="collapsed">
   protected $x = 0;
   protected $y = 0;
   //</editor-fold>
   
   //<editor-fold desc="Constructor" defaultstate="collapsed">
   /**
    * create a new Element object with a 128 bit random number as an id
    */
   public function __construct()
   {
      parent::__construct();
   }

   //</editor-fold>
   
   //<editor-fold desc="Accessor functions" defaultstate="collapsed">
   public function setX($newX)
   {
      $x = $newX;
   }
   public function getX()
   {
      return $x;
   }
   public function setY($newY)
   {
      $y = $newY;
   }
   public function getY()
   {
      return $y;
   }
   /**
    * function that sets both the X and Y values at the same time
    * @param type $newX the X value to be set
    * @param type $newY the Y value to be set
    */
   public function setLocation($newX, $newY)
   {
      $x = $newX;
      $y = $newY;
   }
   /**
    * function that returns both the X and Y values
    * @return type returns an array that contains the X and Y values
    *          index 0: X
    *          index 1: Y
    */
   public function getLocation()
   {
      return array($x,$y);
   }
   //</editor-fold>
}
?>
