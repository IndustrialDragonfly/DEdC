<?php
require_once '../Entity.php';
/**
 * Description of DataFlowDiagram
 *
 * @author Josh Clark
 */
class DataFlowDiagram extends Entity
{

   //<editor-fold desc="Attributes" defaultstate="collapsed">
   protected $externalLinks;  // array of DataFlows
   protected $elementList;    //array of Elements that make up the DFD
   //</editor-fold>
   
   //<editor-fold desc="Constructor" defaultstate="collapsed">
   /**
    * constructor that creates a new DFD object with empty arrays for 
    * externalLinks and elementList
    */
   public function __construct()
   {
      parent::__construct();
      $this->externalLinks = array();
      $this->elementList = array();
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
      if($newLink instanceof DataFlow)
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
    *                 false otherwise
    * @throws BadFunctionCallException if input was of the wrong type
    */
   public function removeExternalLink($link)
   {
      if($link instanceof DataFlow)
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
         if($this->externalLinks[$i]->getId() == $linkId)
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
    * function that adds a new element to the DFD
    * @param Element $newElement
    * @throws BadFunctionCallException
    */
   public function addElement($newElement)
   {
      if($newElement instanceof Element)
      {
         array_push($this->elementList, $newElement);
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
    *                 false if the element was not in the DFD
    * @throws BadFunctionCallException if a bad paramenter is passed
    */
   public function removeElement($element)
   {
      if($element instanceof Element)
      {
         //find if the element is in the list and get its location if it is
         $loc = array_search($element, $this->elementList);
         if ($loc !== false)
         {
            if($element instanceof DataFlow)
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
      if ($index <= count($this->elementList) -1 && $index >= 0)
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
    * @return the specified element or null if not present
    */
   public function getElementById($elementId)
   {
      for ($i = 0; $i < count($this->elementList); $i++)
      {
         if($this->elementList[$i]->getId() == $elementId)
         {
            return $this->elementList[$i];
         }
      }
      return null;
   }
   //</editor-fold>
   //</editor-fold>
}
?>
