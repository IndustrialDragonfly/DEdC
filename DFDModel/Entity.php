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
      // Replaces all instances of +, = or / in the Base64 string with x
      return str_replace(array("+", "=", "/"), array("x","x","x"), 
              base64_encode(openssl_random_pseudo_bytes($numberOfBytes)));
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
   //</editor-fold
   
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
       
       // Figure out the generic type - i.e. Link, Node, SubDFDNode or DataFlowDiagram
       if (is_subclass_of($this, "Diagram"))
       {
           $genericType = "Diagram";
       }
       elseif (is_subclass_of($this, "SubDFDNode"))
       {
           $genericType = "SubDFDNode";
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
           // Throw relevant exception
       }
       
       $entityArray['genericType'] = $genericType;
       
       return $entityArray;
   }
}

?>
