<?php
/**
 * Description of Entity
 *
 * @author Josh Clark
 */
abstract class Entity 
{
   //<editor-fold desc="Attributes" defaultstate="collapsed">
   protected $label;
   protected $id;
   protected $originator;
   protected $organization;
   //</editor-fold>
   
   //<editor-fold desc="Constructor" defaultstate="collapsed">
   /**
    * create a new Entity object with a 128 bit random number as an id
    */
   public function __construct()
   {
      $this->id = $this->generateId();
      $this->label = '';
      $this->originator = '';
      $this->organization = '';
   }
   
   /**
    * function that generates an UUID of length 256 bits
    * @return string a random 256 bit value
    */
   private function generateId()
   {
      $length = 256;
      $numberOfBytes = $length/8;
      return strtr(base64_encode(openssl_random_pseudo_bytes($numberOfBytes)), "+/=", "x");
   }
   //</editor-fold>
   
   //<editor-fold desc="Accessor functions" defaultstate="collapsed">
   //<editor-fold desc="label Accessors" defaultstate="collapsed">
   public function setLabel($newLabel)
   {
      $this->label = $newLabel;
   }
   
   public function getLabel()
   {
      return $this->label;
   }
   //</editor-fold>
   //<editor-fold desc="id Accessors" defaultstate="collapsed">
   public function getId()
   {
      return $this->id;
   }
   //</editor-fold>
   //<editor-fold desc="owner Accessors" defaultstate="collapsed">
   public function setOriginator($newOriginator)
   {
      $this->originator = $newOriginator;
   }
   public function getOriginator()
   {
      return $this->originator;
   }
   //</editor-fold>
   //<editor-fold desc="Organization Accessors" defaultstate="collapsed">
   public function setOrganization($newOrg)
   {
       $this->organization = $newOrg;
   }
   
   public function getOrganization()
   {
       return $this->organization;
   }
   //</editor-fold>
   
   //</editor-fold>
   
}

?>
