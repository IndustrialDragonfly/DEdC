<?php
require_once 'Entity.php';

/**
 * Description of Element
 *
 * @author Josh Clark
 */
 abstract class Element extends Entity
{
   //<editor-fold desc="Attributes" defaultstate="collapsed">
   protected $x;
   protected $y;
   //</editor-fold>
   
   //<editor-fold desc="Constructor" defaultstate="collapsed">
   /**
    * create a new Element object with a 128 bit random number as an id
    */
   public function __construct()
   {
      parent::__construct();
      $this->x = 0;
      $this->y = 0;
   }

   //</editor-fold>
   
   //<editor-fold desc="Accessor functions" defaultstate="collapsed">
   public function setX($newX)
   {
      $this->x = $newX;
   }
   public function getX()
   {
      return $this->x;
   }
   public function setY($newY)
   {
      $this->y = $newY;
   }
   public function getY()
   {
      return $this->y;
   }
   /**
    * function that sets both the X and Y values at the same time
    * @param type $newX the X value to be set
    * @param type $newY the Y value to be set
    */
   public function setLocation($newX, $newY)
   {
      $this->x = $newX;
      $this->y = $newY;
   }
   /**
    * function that returns both the X and Y values
    * @return type returns an array that contains the X and Y values
    *          index 0: X
    *          index 1: Y
    */
   public function getLocation()
   {
      return array($this->x,$this->y);
   }
   //</editor-fold>
}
?>
