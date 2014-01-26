<?php

require_once '../Entity.php';
require_once '../Multiprocess.php';
/**
* Description of DataFlowDiagram
*
* @author Josh Clark
*/
class DataFlowDiagram extends Entity
{
   //<editor-fold desc="Attributes" defaultstate="collapsed">
   protected $externalLinks; // array of DataFlows
   protected $elementList; //array of Elements that make up the DFD

   //</editor-fold>
   //<editor-fold desc="Constructor" defaultstate="collapsed">
   /**
* constructor. if no arguments are specified a new object is created with
* a random id and having empty lists for elements and external links. if
* three arguments are specified, the oject is loaded from the DB if an entry
* with a matching id exists. if parent is null it is assumed that there are
* no external links
* @param PDO $pdo
* @param string $id
* @param Multiprocess $parent
*/
   public function __construct()
   {
      if (func_num_args() == 0)
      {
         parent::__construct();
         $this->externalLinks = array();
         $this->elementList = array();
      }
      else if (func_num_args() == 3)
      {
         parent::__construct();
         $this->externalLinks = array();
         $this->elementList = array();
         $pdo = func_get_arg(0);
         $this->id = func_get_arg(1);
         // The owning multiprocess
         $parent = func_get_arg(2);
         
         //get the id, label and the originator of the dfd from the database
         $mySQLstatement = $pdo->prepare("SELECT * FROM entity WHERE id=?");
         $mySQLstatement->bindParam(1, $this->getId());
         $mySQLstatement->execute();
         $Entity_var = $mySQLstatement->fetch();
         if($Entity_var == FALSE )
         {
            throw new BadFunctionCallException("no matching id found in entity DB");
         }
         $this->id = $Entity_var['id'];
         $this->label = $Entity_var['label'];
         $this->originator = $Entity_var['originator'];
         
         //handle the element list
         $mySQLstatement = $pdo->prepare("SELECT * FROM element_list WHERE dfd_id=?");
         $mySQLstatement->bindParam(1, $this->getId());
         $mySQLstatement->execute();
         
         //extract all the ids of the elements
         $id_list = array();
         $newId = $mySQLstatement->fetch();
         while ($newId != FALSE)
         {
            array_push($id_list,$newId['el_id']);
            $newId = $mySQLstatement->fetch();
         }
         //go though the list of ids and load all of the one that are not dataflows
         for ($i = 0; $i < count($id_list); $i++)
         {
            //check to see if the element is not a DataFlow
            //if it is
            //load the element from the DB and add it to the element list
            
            $mySQLstatement = $pdo->prepare("SELECT type FROM entity WHERE id=?");
            $mySQLstatement->bindParam(1, $id_list[$i]);
            $mySQLstatement->execute();
            $type = $mySQLstatement->fetch();
            $newElement = new $type['type']($pdo, $id_list[$i], $this);
            $this->addElement($newElement);
         }
         
         //handle external links
         if ($parent != null)
         {
            for ($i = 0; $i < $parent->getNumberOfLinks(); $i++)
            {
               $this->addExternalLink($parent->getLinkbyPosition($i));
            }
         }
         
         
      }
   }

   //</editor-fold>
   //<editor-fold desc="Accessor functions" defaultstate="collapsed">
   //<editor-fold desc="externalLinks functions" defaultstate="collapsed">
   /**
* function that returns how many external links this DFD has
* @return integer
*/
   public function getNumberOfExternalLinks()
   {
      return count($this->externalLinks);
   }

   /**
* function that adds a new extenal Link to the list
* @param DataFlow $newlink
* @throws BadFunctionCallException
*/
   public function addExternalLink($newLink)
   {
      if ($newLink instanceof DataFlow)
      {
         array_push($this->externalLinks, $newLink);
      }
      else
      {
         throw new BadFunctionCallException("input parameter was not a DataFlow");
      }
   }

   /**
* function that removes an external Link from the list
* @param DataFlow $link
* @return boolean true if the link was present and if it was removed
* false otherwise
* @throws BadFunctionCallException if input was of the wrong type
*/
   public function removeExternalLink($link)
   {
      if ($link instanceof DataFlow)
      {
         //find if the link is in the list and get its location if it is
         $loc = array_search($link, $this->externalLinks);
         if ($loc !== false)
         {
            //remove the link from the list
            unset($this->externalLinks[$loc]);
            //normalize the indexes of the list
            $this->externalLinks = array_values($this->externalLinks);
            return true;
         }
         else
         {
            return false;
         }
      }
      else
      {
         throw new BadFunctionCallException("input parameter was not a DataFlow");
      }
   }

   /**
* function that returns an external link based uppon its position in the list
* @param integer $index
* @return DataFlow
* @throws BadFunctionCallException if the input parameter was out of bounds
*/
   public function getExternalLinkByPosition($index)
   {
      if ($index <= count($this->externalLinks) - 1 && $index >= 0)
      {
         return $this->externalLinks[$index];
      }
      else
      {
         throw new BadFunctionCallException("input parameter was out of bounds");
      }
   }

   /**
* function that returns an external link based uppon it id
* @param string $linkId
* @return DataFlow will return the specified link
*/
   public function getExternalLinkbyId($linkId)
   {
      for ($i = 0; $i < count($this->externalLinks); $i++)
      {
         if ($this->externalLinks[$i]->getId() == $linkId)
         {
            return $this->externalLinks[$i];
         }
      }
      return null;
   }

   //</editor-fold>
   //<editor-fold desc="elementList functions" defaultstate="collapsed">
   /**
* function that returns the number of elements in this DFD
* @return integer
*/
   public function getNumberOfElements()
   {
      return count($this->elementList);
   }

   /**
* function that adds a new element to the DFD and sets this DFD as the
* element's parent
* @param Element $newElement
* @throws BadFunctionCallException
*/
   public function addElement($newElement)
   {
      if ($newElement instanceof Element)
      {
         array_push($this->elementList, $newElement);
         $newElement->setParent($this);
      }
      else
      {
         throw new BadFunctionCallException("input parameter was not an Element");
      }
   }

   /**
* function that removes a specified element from the DFD
* @param Element $element
* @return boolean true if the element was in the DFD and was removed
* false if the element was not in the DFD
* @throws BadFunctionCallException if a bad paramenter is passed
*/
   public function removeElement($element)
   {
      if ($element instanceof Element)
      {
         //find if the element is in the list and get its location if it is
         $loc = array_search($element, $this->elementList);
         if ($loc !== false)
         {
            if ($element instanceof DataFlow)
            {
               $element->removeAllLinks();
            }
            elseif ($element instanceof Node)
            {
               $element->removeAllLinks();
            }
            //remove the element from the list
            unset($this->elementList[$loc]);
            //normalize the indexes of the list
            $this->elementList = array_values($this->elementList);
            return true;
         }
         else
         {
            return false;
         }
      }
      else
      {
         throw new BadFunctionCallException("input parameter was not a Element");
      }
   }

   /**
* retrieves and element from the list of objects, use getby ID instead
* @param integer $index
* @return Element
* @throws BadFunctionCallException
*/
   public function getElementByPosition($index)
   {
      if ($index <= count($this->elementList) - 1 && $index >= 0)
      {
         return $this->elementList[$index];
      }
      else
      {
         throw new BadFunctionCallException("input parameter was out of bounds");
      }
   }

   /**
* fuction the returns a specific element from the list if it present, will return null if the element was not present
* @param string $elementId
* @return Element the specified element or null if not present
*/
   public function getElementById($elementId)
   {
      for ($i = 0; $i < count($this->elementList); $i++)
      {
         if ($this->elementList[$i]->getId() == $elementId)
         {
            return $this->elementList[$i];
         }
      }
      return null;
   }

   //</editor-fold>
   //</editor-fold>
   //<editor-fold desc="DB functions" defaultstate="collapsed">
   /**
* function that will save this object to the database
* this will also save every element in the element list
* @param PDO $pdo this is the connection to the Database
*/
   public function save($pdo)
   {
      //<editor-fold desc="save to Entity table" defaultstate="collapsed">
      // Prepare the statement
      $insert_stmt = $pdo->prepare("INSERT INTO entity (id, label, type, originator) VALUES(?,?,?,?)");

      // Bind the parameters of the prepared statement
      $type = Types::DataFlowDiagram;
      $insert_stmt->bindParam(1, $this->id);
      $insert_stmt->bindParam(2, $this->label);
      $insert_stmt->bindParam(3, $type);
      $insert_stmt->bindParam(4, $this->originator);

      // Execute, catch any errors resulting
      $insert_stmt->execute();
      //</editor-fold>
      //<editor-fold desc="save to Element List table" defaultstate="collapsed">
      // Prepare the statement
      $insert_stmt = $pdo->prepare("INSERT INTO element_list (dfd_id, el_id) VALUES(?,?)");
      for ($i = 0; $i < $this->getNumberOfElements(); $i++)
      {
         // Bind the parameters of the prepared statement
         $insert_stmt->bindParam(1, $this->id);
         $insert_stmt->bindParam(2, $this->elementList[$i]->getId());
         // Execute, catch any errors resulting
         $insert_stmt->execute();
      }
      //</editor-fold>
      //<editor-fold desc="save to External link List table" defaultstate="collapsed">
      // Prepare the statement
      $insert_stmt = $pdo->prepare("INSERT INTO external_links (dfd_id, df_id) VALUES(?,?)");
      for ($i = 0; $i < $this->getNumberOfExternalLinks(); $i++)
      {
         // Bind the parameters of the prepared statement
         $insert_stmt->bindParam(1, $this->id);
         $insert_stmt->bindParam(2, $this->externalLinks[$i]->getId());
         // Execute, catch any errors resulting
         $insert_stmt->execute();
      }
      //</editor-fold>
   }

   //</editor-fold>
}
?>