<?php
include_once 'Element.php';
/**
 * Description of Node
 *
 * @author Josh Clark
 */
class Node extends Element
{
   //<editor-fold desc="Attributes" defaultstate="collapsed">
   protected $links;
   //</editor-fold>
   
   //<editor-fold desc="Constructor" defaultstate="collapsed">
   public function __construct()
   {
      parent::__construct();
      $links = array();
   }

   //</editor-fold>
   
   //<editor-fold desc="Accessor functions" defaultstate="collapsed">
   
   /**
    * function that gets the number of links that connect to this node
    * @return type the number of links 
    */
   public function getNumberOfLinks()
   {
      return count($links);
   }
   
   /**
    * function that adds a new link to the list of links
    * @param DataFlow $newLink
    * @throws BadFunctionCallException
    */
   public function addLink($newLink)
   {
      if($newLink instanceof DataFlow)
      {
         array_push($links, $newLink);
      }
      else
      {
         throw new BadFunctionCallException("input parameter was not a DataFlow");
      }
   }
   
   /**
    * removes a specified DataFlow from the list of links
    * @param type $link the link to be removed
    * @return boolean if the link was in the array
    * @throws BadFunctionCallException if the input was not a DataFlow
    */   
   public function removeLink($link)
   {
      if($link instanceof DataFlow)
      {
         //find if the link is in the list and get its location if it is
         $loc = array_search($link, $links);
         if ($loc != false)
         {
            //code to find if this Node is the DataFlows orgin or destination
            if(isOrigin() == true)
            {
               //clear the origin of the link
               $link->clearOrginNode();
            }
            else
            {
               // clear the destination of the link
               $link->clearDestinationNode();
            }
            //remove the link from the list
            unset($links($loc));
            //normalize the indexes of the list
            $links = array_values($links);
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
   
   private function isOrgin($link)
   {
      if($link instanceof DataFlow)
      {
         if ($this == $link->getOriginNode())
         {
            return TRUE;
         }
         elseif ($this == $link->getDestinationNode())
         {
            return FALSE;
         }
         else
         {
            throw new BadFunctionCallException("This DataFlow is not connected to this Node");
         }
      }
      else 
      {
         throw new BadFunctionCallException("input parameter was not a DataFlow");
      }
   }
   
   /**
    * Returns a specified link based uppon where it is in the list
    * @param type $index integer
    * @return type DataFlow
    * @throws BadFunctionCallException if the value was out of bounds
    */
   public function getLinkbyPosition($index)
   {
      if ($index <= count($links) )
      {
         return $links[$index];
      }
      else
      {
         throw new BadFunctionCallException("input parameter was out of bounds");
      }
   }
   
   /**
    * a function that searches the list of DataFlows and returns one with a matching id
    * @param type $linkId
    * @return a DataFlow that has an id matching the specified one, will return null if not specified
    */
   public function getLinkbyId($linkId)
   {
      for ($i = 0; $i < count($links); $i++)
      {
         if($links[$i]->getId() == $linkId)
         {
            return $links[$i];
         }
      }
      return null;
   }
   
   /**
    * function that removes every link to this node
    */
   public function removeAllLinks()
   {
      while(count($links) != 0)
      {
         removeLink($links[0]);
      }
   }
   //</editor-fold>
}
?>
