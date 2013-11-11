<?php
require_once 'Node.php';
require_once 'DataFlowDiagram.php';
/**
 * Description of Multiprocess
 *
 * @author Josh Clark
 */
class Multiprocess extends Node
{
   //<editor-fold desc="Attributes" defaultstate="collapsed">
   protected $subDataFlowDiagram;
   //</editor-fold>
   
   //<editor-fold desc="Constructor" defaultstate="collapsed">
   public function __construct()
   {
      parent::__construct();
      $this->subDataFlowDiagram = new DataFlowDiagram;
   }

   //</editor-fold>
   
   //<editor-fold desc="Accessor functions" defaultstate="collapsed">
   public function getSubDFD()
   {
      return $this->subDataFlowDiagram;
   }
   /**
    * 
    * @param DataFlowDiagram $aDiagram a new DFD to set the sub DFD to
    * @throws BadFunctionCallException if the input is not a DFD
    */
   public function setSubDFD($aDiagram)
   {
      if($aDiagram instanceof DataFlowDiagram)
      {
         $this->subDataFlowDiagram = $aDiagram;
      }
      else
      {
         throw new BadFunctionCallException("input parameter was not a DataFlowDiagram");
      }
   }
   //</editor-fold>
   
   //<editor-fold desc="overriding  functions" defaultstate="collapsed">
   /**
    * function that adds a new link to the list of links
    * @param DataFlow $newLink
    * @throws BadFunctionCallException
    */
   public function addLink($newLink)
   {
      if($newLink instanceof DataFlow)
      {
         array_push($this->links, $newLink);
         $this->subDataFlowDiagram->addExternalLink($newLink);
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
         $loc = array_search($link, $this->links, true);
         if ($loc !== false)
         {
            //remove the link from the list
            unset($this->links[$loc]);
            //normalize the indexes of the list
            $this->links = array_values($this->links);
            $this->subDataFlowDiagram->removeExternalLink($link);
            //code to find if this Node is the DataFlows orgin or destination
            if($this->isOrigin($link) == true)
            {
               //clear the origin of the link
               $link->clearOriginNode();
            }
            else
            {
               // clear the destination of the link
               $link->clearDestinationNode();
            }
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
   
   
   //</editor-fold>
}
?>
