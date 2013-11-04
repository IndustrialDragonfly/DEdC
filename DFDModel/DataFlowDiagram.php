<?php
include_once 'Entity.php';
/**
 * Description of DataFlowDiagram
 *
 * @author Josh Clark
 */
class DataFlowDiagram extends Entity
{

   //<editor-fold desc="Attributes" defaultstate="collapsed">
   protected $externalLinks;
   protected $elementList;
   //</editor-fold>
   
   //<editor-fold desc="Constructor" defaultstate="collapsed">
   public function __construct()
   {
      parent::__construct();
      $externalLinks = array();
      $elementList = array();
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
      return count($externalLinks);
   }
   
   /**
    * function that adds a new extenal Link to the list
    * @param type $newlink 
    * @throws BadFunctionCallException
    */
   public function addExternalLink($newlink)
   {
      if($newLink instanceof DataFlow)
      {
         array_push($externalLinks, $newLink);
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
         $loc = array_search($link, $externalLinks);
         if ($loc != false)
         {
            //remove the link from the list
            unset($externalLinks($loc));
            //normalize the indexes of the list
            $externalLinks = array_values($externalLinks);
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
    * @param type $index integer
    * @return DataFlow 
    * @throws BadFunctionCallException if the input parameter was out of bounds
    */
   public function getExternalLinkByPosition($index)
   {
      if ($index <= count($externalLinks) )
      {
         return $externalLinks[$index];
      }
      else
      {
         throw new BadFunctionCallException("input parameter was out of bounds");
      }
   }
   
   /**
    * function that returns an external link based uppon it id
    * @param type $linkId
    * @return DataFlow will return the 
    */
   public function getExternalLinkbyId($linkId)
   {
      for ($i = 0; $i < count($externalLinks); $i++)
      {
         if($externalLinks[$i]->getId() == $linkId)
         {
            return $externalLinks[$i];
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
      return count($elementList);
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
         array_push($elementList, $newElement);
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
         $loc = array_search($element, $elementList);
         if ($loc != false)
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
            unset($elementList($loc));
            //normalize the indexes of the list
            $elementList = array_values($elementList);
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
   
   
   public function getElementByPosition($index)
   {
      if ($index <= count($elementList) )
      {
         return $elementList[$index];
      }
      else
      {
         throw new BadFunctionCallException("input parameter was out of bounds");
      }
   }
   
   
   public function getElementById($elementId)
   {
      for ($i = 0; $i < count($elementList); $i++)
      {
         if($elementList[$i]->getId() == $elementId)
         {
            return $elementList[$i];
         }
      }
      return null;
   }
   //</editor-fold>
   //</editor-fold>
}
?>
