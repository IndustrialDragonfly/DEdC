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
       // TODO - handle setting storage, and armor this constructor against issues
       // TODO - this constructor should know when it should set things to be null and when it is being loaded other ways
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
      // Replaces all instances of +, = or / in the Base64 string with x
      return str_replace(array("+", "=", "/"), array("x","x","x"), 
              base64_encode(openssl_random_pseudo_bytes($numberOfBytes)));
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
   /* disabled
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
   
   /**
    * Returns an assocative array representing the entity object. This 
    * assocative array has the following elements and types:
    * id String
    * label String
    * originator String
    * organization String
    * type String
    * genericType String
    *  
    * @returns Mixed[]
    */
   public function getAssociativeArray()
   {
       $entityArray = array();
       
       $entityArray['id'] = $this->id;
       $entityArray['label'] = $this->label;
       $entityArray['originator'] = $this->originator;
       $entityArray['organization'] = $this->organization;
       $entityArray['type'] = get_class($this);
       
       $genericType = NULL;
       
       // Figure out the generic type - i.e. Link, Node, diaNode or DataFlowDiagram
       if (is_subclass_of($this, "Diagram"))
       {
           $genericType = "Diagram";
       }
       elseif (is_subclass_of($this, "DiaNode"))
       {
           $genericType = "diaNode";
       }
       elseif (is_subclass_of($this, "Node"))
       {
           $genericType = "Node";
       }
       elseif (is_subclass_of($this, "Link"))
       {
           $genericType = "Link";
       }
       else
       {
           // TODO - Throw relevant exception
       }
       
       $entityArray['genericType'] = $genericType;
       
       return $entityArray;
   }
   
       /**
     * Takes an assocative array representing an object and loads it into this
     * object.
     * @param Mixed[] $assocativeArray
     */
   protected function loadAssociativeArray($associativeArray)
   {
       // TODO - error handling for missing elements/invalid elements
        $this->id = $associativeArray['id'];
        $this->label = $associativeArray['label'];
        $this->originator = $associativeArray['originator'];
        $this->organization = $associativeArray['organization'];
   }
}

?>
