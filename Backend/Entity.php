<?php
/**
 * Description of Entity
 *
 * @author Josh Clark
 */
class Entity 
{
   //<editor-fold desc="Attributes" defaultstate="collapsed">
   protected $label = "";
   protected $id = "";
   protected $owner = "";
   
   //</editor-fold>
   
   //<editor-fold desc="Constructor" defaultstate="collapsed">
   /**
    * create a new Entity object with a 128 bit random number as an id
    */
   public function __construct()
   {
      $id = generateId();
   }
   
   /**
    * function that generates an UUID of length 128 bits
    * @return type a random 128 bit value
    */
   private function generateId()
   {
      return strtr(base64_encode(openssl_random_pseudo_bytes(128)), "+/=", "xxx");
   }
   //</editor-fold>
   
   //<editor-fold desc="Accessor functions" defaultstate="collapsed">
   //<editor-fold desc="label Accessors" defaultstate="collapsed">
   public function setLabel($newLabel)
   {
      $label = $newLabel;
   }
   
   public function getLabel()
   {
      return $label;
   }
   //</editor-fold>
   //<editor-fold desc="id Accessors" defaultstate="collapsed">
   public function getId()
   {
      return $id;
   }
   //</editor-fold>
   //<editor-fold desc="owner Accessors" defaultstate="collapsed">
   public function setOwner($newOwner)
   {
      $owner = $newOwner;
   }
   public function getOwner()
   {
      return $owner;
   }
   //</editor-fold>
   
   //</editor-fold>
   
}

?>
