<?php
/**
 * This class represents an object that has a UUID, a label and can be stored 
 * into some manner of storage medium
 * 
 * Known direct subclasses:
 *    Element
 *    DataFlowDiagram
 *
 * @author Josh Clark
 */
abstract class Entity 
{
   //<editor-fold desc="Attributes" defaultstate="collapsed">
   /**
    * This is a container which holds the name of the object
    * @var String
    */
   protected $label;
   
   /**
    * This contains a universally unique identifier
    * @var String
    */
   protected $id;
   
   /**
     * UUID of the originator of this DFD
     * @var String 
     */
   protected $originator;
   
   /**
    * This is a container for the organization that this object belongs to
    * @var String
    */
   protected $organization;
   
   
   /**
    * Storage object, should be readable and/or writable (depending on whether
    * this is a normal data store, import data source, or export data format)
    * @var Readable/Writable
    */
   protected $storage;
   
   //</editor-fold>
   
   //<editor-fold desc="Constructor" defaultstate="collapsed">
   /**
    * This creates a new Entity object with a 256 bit random number as an id
    */
   public function __construct()
   {
      $this->id = $this->generateId();
      $this->label = '';
      $this->originator = '';
      $this->organization = '';
   }
   
   /**
    * This is a function that generates a UUID String with a length of 265 bits
    * @return String
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
   /**
    * This is a function that sets the label of this object
    * @param String $newLabel
    */
   public function setLabel($newLabel)
   {
      $this->label = $newLabel;
   }
   /**
    * This is a function that returns the label of the current opject
    * @return String 
    */
   public function getLabel()
   {
      return $this->label;
   }
   //</editor-fold>
   //<editor-fold desc="id Accessors" defaultstate="collapsed">
   /**
    * This function returns the UUID of this object
    * @return String
    */
   
   public function getId()
   {
      return $this->id;
   }
   //intentionally no setId()
   //</editor-fold>
   //<editor-fold desc="owner Accessors" defaultstate="collapsed">
   /**
    * This is a function that sets the Originator of this object
    * @param String $newOriginator
    */
   public function setOriginator($newOriginator)
   {
      $this->originator = $newOriginator;
   }
   
   /**
    * This is a function that retrieves the Originator of this object
    * @return String
    */
   public function getOriginator()
   {
      return $this->originator;
   }
   //</editor-fold>
   //<editor-fold desc="Organization Accessors" defaultstate="collapsed">
   /**
    * This is a function that sets the Organization that this object belongs to
    * @param String $newOrg
    */
   public function setOrganization($newOrg)
   {
       $this->organization = $newOrg;
   }
   
   /**
    * This is a function that retrieves the Organization that this object 
    * belongs to
    * @return String
    */
   public function getOrganization()
   {
       return $this->organization;
   }
   //</editor-fold>
   //<editor-fold desc="Storage Accessors" defaultstate="collapsed">
   /**
    * This a a function that will set the storage object that is associated 
    * with this object
    * 
    * should this function exist?
    *   -nope
    * 
    * @param Readable/Writable $newStorage
    */
   /*
   public function setStorage($newStorage)
   {
      $this->storage = $newStorage;
   }*/
   
   /**
    * This is a function that retrieves the Storage object that is associated 
    * with this object
    * @return Readable/Writable
    */
   public function getStorage()
   {
      return $this->storage;
   }
   //</editor-fold>
   
   //</editor-fold>
   
}

?>
