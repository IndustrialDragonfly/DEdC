<?php

require_once 'Node.php';
require_once 'DataFlowDiagram.php';
/**
 * Description of DiaNode
 *
 * @author Josh Clark
 * @author Eugene Davis
 */
class DiaNode extends Node
{
    //<editor-fold desc="Attributes" defaultstate="collapsed">
    protected $subDiagram;

    //</editor-fold>
    //<editor-fold desc="Constructor" defaultstate="collapsed">

    /**
     * Constructs the DiaNode. Always requires a storage object. If passed an ID of
     * an existing DiaNode, loads that from the storage object. If passed the ID
     * of an existing Diagram, creates a new DiaNode in that object. If passed
     * an associativeArray which represents a DiaNode, loads that.
     * @param {Read,Write}Storable $storage
     * @param String $id (Optional if associative array is passed instead)
     * @param Mixed[] $associativeArray (Optionial if ID is passed instead)
     */
    public function __construct()
    {
        if (func_num_args() == 2)
        {
            // check the second parameter 
            if (is_string(func_get_arg(1)))
            {
                //the second parameter is an id
                //check to see if the second parameter is an id of a Diagram or a DiaNode
                $type = func_get_arg(0)->getTypeFromUUID(func_get_arg(1));
                if (is_subclass_of($type, "DiaNode"))
                {
                    //if the id is for a DiaNode object load the object from storage
                    $this->id = func_get_arg(1);
                    $this->storage = func_get_arg(0);
                    if (!is_subclass_of($this->storage, "ReadStorable"))
                    {
                        throw new BadConstructorCallException("Passed storage object does not implement ReadStorable.");
                    }
                    if (!is_subclass_of($this->storage, "WriteStorable"))
                    {
                        throw new BadConstructorCallException("Passed storage object does not implement WriteStorable.");
                    }
                    $assocativeArray = $this->storage->loadDiaNode($this->id);
                    $this->loadAssociativeArray($assocativeArray);
                }
                else
                {
                    throw new BadConstructorCallException("Passed ID was for neither a Node nor a Diagram.");
                }
            }
            else if (is_array(func_get_arg(1)))
            {
                // if the second parameter was an array pass this constructor on to the parent constructor
                parent::__construct(func_get_arg(0), func_get_arg(1));
            }
            else
            {
                throw new BadConstructorCallException("Invalid second parameter, can be neither an ID or an assocative array");
            }
        }
        else
        {
            throw new BadConstructorCallException("Invalid number of input parameters were passed to the DiaNode Constructor");
        }
    }

    //</editor-fold>
    //<editor-fold desc="Accessor functions" defaultstate="collapsed">
    /**
     *
     * @return DataFlowDiagram
     */
    public function getSubDFD()
    {
        return $this->subDiagram;
    }

    /**
     *
     * @param DataFlowDiagram $aDiagram a new DFD to set the sub DFD to
     * @throws BadFunctionCallException if the input is not a DFD
     */
    public function setSubDFD($aDiagram)
    {
        if ($aDiagram instanceof DataFlowDiagram)
        {
            $this->subDiagram = $aDiagram;
        }
        else
        {
            throw new BadFunctionCallException("input parameter was not a DataFlowDiagram");
        }
    }

    /**
     * Returns an assocative array representing the entity object. This 
     * assocative array has the following elements and types:
     * id String
     * label String
     * originator String
     * organization String 
     * type String
     * genericType String
     * x Int
     * y Int
     * parent String
     * links String[]
     * subDataFlowDiagram String
     * 
     * @return Mixed
     */
    public function getAssociativeArray()
    {
        $diaNodeArray = parent::getAssociativeArray();
        $diaNodeArray['subDataFlowDiagram'] = $this->subDiagram;

        return $diaNodeArray;
    }

    /**
     * Takes an assocative array representing an object and loads it into this
     * object.
     * @param Mixed[] $assocativeArray
     */
    public function loadAssociativeArray($associativeArray)
    {
        parent::loadAssociativeArray($associativeArray);

        // TODO - error handling for missing elements/invalid elements
        $this->subDiagram = $associativeArray['diagramId'];
    }

    //</editor-fold>
    //<editor-fold desc="overriding functions" defaultstate="collapsed">
    /**
     * function that adds a new link to the list of links
     * @param DataFlow $newLink
     * @throws BadFunctionCallException
     */
    public function addLink($newLink)
    {
        parent::addLink($newLink);
        // Check if this is equal to null - if it is, this can't happen yet
        // This function as such must be called when a new DFD is linked up to
        // this diaNode
        if ($this->subDiagram !== NULL)
        {
            if (is_subclass_of($newLink, "Link"))
            {
                $this->subDiagram->addExternalLink($newLink);
            }
            else
            {
                throw new BadFunctionCallException("Input parameter was not a Link");
            }
        }
    }

    /**
     * Removes a specified DataFlow from the list of links
     * @param type $link the link to be removed
     * @return boolean if the link was in the array
     * @throws BadFunctionCallException if the input was not a DataFlow
     */
    public function removeLink($link)
    {
        // If removed the link from the Node object and subDFD exists, remove from
        // the subDFD
        if (parent::removeLinks() && $this->subDiagram != NULL)
        {
            $this->subDiagram->removeExternalLink($link);
        }
    }

    //</editor-fold>
    //<editor-fold desc="Save/Delete/Update" defaultstate="collapsed">
    /**
     * function that will save this object to the database
     */
    public function save()
    {
        // Call the Node object save function to do most of the work
        parent::save();
        // Call storage object's saveDiaNode
        $this->storage->saveDiaNode($this->subDiagram, $this->id);
    }

    /**
     * Function that deletes this object from the database
     */
    public function delete()
    {
        // Call the parent delete function AFTER child delete function
        $this->storage->deleteDiaNode($this->id);

        parent::delete();
    }

    /**
     * Refreshes the object in the storage medium, probably should later have a 
     * dedicated function in the storage medium in the future.
     */
    public function update()
    {
        // Cannot call removeAllLinks in Node delete function
        $this->storage->deleteDiaNode($this->id);
        $this->storage->deleteNode($this->id);
        $this->save();
    }

    //</editor-fold>
}
?>

