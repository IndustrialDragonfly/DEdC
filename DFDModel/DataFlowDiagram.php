<?php

require_once '../Entity.php';
require_once '../Multiprocess.php';
/**
 * This class represents a Dataflow Diagram, which contains nodes and thier 
 * connections.
 * 
 * inherits from Entity
 *
 * @author Josh Clark
 * @author Eugene Davis
 */
class DataFlowDiagram extends Entity
{
   //<editor-fold desc="Attributes" defaultstate="collapsed">
   /**
    * Stack of the ancestors of this DFD, starting at the root DFD
    * and going back to the immediate parent.
    * Used to figure out how to route links that connect outside of this DFD.
    * Can be null if root
    * @var String[]
    */
   protected $ancestry;

   /**
    * List of all nodes contained within this DFD and basic data to use them.
    * Stored in an associative array.
    * @var String[]
    */
   protected $nodeList;

   /**
    * List of all links contained within this DFD and basic data to used them.
    * Stored in an associative array.
    * @var String[]
    */
   protected $linkList;

   /**
    * List of all the the subDFDNodes contained with this DFD and basic data
    * for the frontend to use them. Stored in an associative array.
    * @var Mixed[]
    */
   protected $subDFDNodeList;

   /**
    * The SubDFDNode UUID that this DFD is linked to 
    * Can be null if root
    * @var String 
    */
   protected $subDFDNode;

   //</editor-fold>
   //<editor-fold desc="Constructor" defaultstate="collapsed">
   /**
    * Constructor. If no arguments are specified a new object is created with
    * a random id and having empty lists for elements and external links. If
    * three arguments are specified, the oject is loaded from the DB if an entry
    * with a matching id exists. If parent is null it is assumed that there are
    * no external links
    * @param Readable/Writable $storage
    * @param string $id
    */
   public function __construct()
   {
      parent::__construct();
      // If there is only one argument (the storage object) then this is a
      // root DFD
      // DataFlowDiagram($storage)
      if (func_num_args() == 1)
      {
         $this->elementList = array();
         $this->parentStack = null;
         $this->subDFDNode = null;
         $this->storage = func_get_arg(0);
      }
      // If creating a new DFD connected to a SubDFDNode
      // DataFlowDiagram($storage, $SubDFDNode)
      else if (func_num_args() == 2 &&
              is_subclass_of($this->storage->getTypeFromUUID(func_get_arg(1)), "SubDFDNode"))
      {
         $this->storage = func_get_arg(0);
         $this->elementList = array();
         $this->subDFDNode = func_get_arg(1);

         // Initialize the linked SubDFDNode so we can get its parent and link
         // ourselves into it
         $type = getTypeFromUUID($this->subDFDNode);
         $subDFDNode = new $type($this->storage);

         $parentDFD_id = $subDFDNode->getParent();

         // Initialize the parent DFD and get its ancenstry
         $type = getTypeFromUUID($parentDFD_id);
         $parentDFD = new $type($parentDFD_id);
         $this->ancestry = $parentDFD->getAncestry();
         // Add immediate parent to stack
         array_push($this->ancestry, $parentDFD->getId());
      }
      // If this is an existing DFD to load, get the ID of the DFD
      // DataFlowDiagram($storage, $DFD)
      else if (func_num_args() == 2 &&
              is_subclass_of($this->storage->getTypeFromUUID(func_get_arg(1)), "DataFlowDiagram"))
      {
         $this->elementList = array();
         $this->storage = func_get_arg(0);
         $this->id = func_get_arg(1);
         $vars = $this->storage->loadDFD($this->id);

         // Load up values from the associative array
         $this->label = $vars['label'];
         $this->originator = $vars['originator'];
         $this->nodeList = $vars['nodeList'];
         $this->linkList = $vars['linkList'];
         $this->subDFDNodeList = $vars['subDFDNodeList'];
         $this->ancestry = $vars['ancestry'];
      }
   }

   //</editor-fold>
   //<editor-fold desc="Accessor functions" defaultstate="collapsed">
   //<editor-fold desc="elementList functions" defaultstate="collapsed">
   /**
    * function that returns the number of nodes in this DFD
    * @return int
    */
   public function getNumberOfNodes()
   {
      return count($this->nodeList);
   }

   /**
    * function that adds a new element to the DFD and sets this DFD as the
    * element's parent
    * @param Element $newElement
    * @throws BadFunctionCallException
    */
   public function addNode($newNode)
   {
      if ($newNode instanceof Node)
      {
         array_push($this->nodeList, $newNode);
         $newNode->setParent($this);
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
   //<editor-fold desc="linkList functions" defaultstate="collapsed">
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