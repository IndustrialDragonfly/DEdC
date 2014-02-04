<?php
require_once 'Entity.php';

/**
 * this class reresents an abstract object in a DFD which has a everything in an 
 * Enity in addition to a X-Y coordinate and a parent DFD 
 * 
 * known direct subclasses:
 *    Node
 *    Link
 * 
 * inherits from Entity
 *
 * @author Josh Clark
 */
 abstract class Element extends Entity
{
   //<editor-fold desc="Attributes" defaultstate="collapsed">
   /**
* the x coordinate of this element
* @var int
*/
   protected $x;
   /**
* the y coordinate of this element
* @var int
*/
   protected $y;
   /**
   * the UUID of the parent DFD that contains this element
   * @var type String
   */
   protected $parent;
   
   
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
   //<editor-fold desc="X Y Accessors" defaultstate="collapsed">
   /**
    * A function that sets the x coordinate of this element
    * @param int $newX
    */
   public function setX($newX)
   {
      $this->x = $newX;
   }
   
   /**
    * A function that returns the X coordinate of this element
    * @return int
    */
   public function getX()
   {
      return $this->x;
   }
   
   /**
    * A function that sets the Y coordinate of this element
    * @param int $newY
    */
   public function setY($newY)
   {
      $this->y = $newY;
   }
   
   /**
    * a function that returns the Y coordinate of this element
    * @return type
    */
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
   * index 0: X
   * index 1: Y
   */
   public function getLocation()
   {
      return array($this->x,$this->y);
   }
   //</editor-fold>
   
   //<editor-fold desc="Parent Accessors" defaultstate="collapsed">
   /**
   * function that changest the parent DFD of this element
   * @param String $newParent
   */
   public function setParent($newParent)
   {
      $this->parent = $newParent;
   }
   
   /**
   * function that retrieves the UUID of the current parent of this element
   * @return String
   */
   public function getParent()
   {
      return $this->parent;
   }
   //</editor-fold>
   //</editor-fold>
}
?>