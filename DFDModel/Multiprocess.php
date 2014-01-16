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
    /**
     * 
     * @return DataFlowDiagram
     */
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
        if ($aDiagram instanceof DataFlowDiagram)
        {
            $this->subDataFlowDiagram = $aDiagram;
        } else
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
        if ($newLink instanceof DataFlow)
        {
            array_push($this->links, $newLink);
            $this->subDataFlowDiagram->addExternalLink($newLink);
        } else
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
        if ($link instanceof DataFlow)
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
                if ($this->isOrigin($link) == true)
                {
                    //clear the origin of the link
                    $link->clearOriginNode();
                } else
                {
                    // clear the destination of the link
                    $link->clearDestinationNode();
                }
                return true;
            } else
            {
                return false;
            }
        } else
        {
            throw new BadFunctionCallException("input parameter was not a DataFlow");
        }
    }

    //</editor-fold>
    //<editor-fold desc="DB functions" defaultstate="collapsed">
    /**
     * function that will save this object to the database
     * @param PDO $pdo this is the connection to the Database
     */
    public function save($pdo)
    {
        //<editor-fold desc="save to Entity table" defaultstate="collapsed">
        // Prepare the statement
        $insert_stmt = $pdo->prepare("INSERT INTO entity (id, label, type, originator) VALUES(?,?,?,?)");

        // Bind the parameters of the prepared statement
        $type = Types::Multiprocess;
        $insert_stmt->bindParam(1, $this->id);
        $insert_stmt->bindParam(2, $this->label);
        $insert_stmt->bindParam(3, $type);
        $insert_stmt->bindParam(4, $this->originator);

        // Execute, catch any errors resulting
        $insert_stmt->execute();
        //</editor-fold>
        //<editor-fold desc="save to Element table" defaultstate="collapsed">
        // Prepare the statement
        $insert_stmt = $pdo->prepare("INSERT INTO element (id, x, y) VALUES(?,?,?)");

        // Bind the parameters of the prepared statement
        $insert_stmt->bindParam(1, $this->id);
        $insert_stmt->bindParam(2, $this->x);
        $insert_stmt->bindParam(3, $this->y);

        // Execute, catch any errors resulting
        $insert_stmt->execute();
        //</editor-fold>
        //<editor-fold desc="save to Node table" defaultstate="collapsed">
        // Prepare the statement
        $insert_stmt = $pdo->prepare("INSERT INTO node (id, df_id) VALUES(?,?)");
        for ($i = 0; $i < $this->getNumberOfLinks(); $i++)
        {
            // Bind the parameters of the prepared statement
            $insert_stmt->bindParam(1, $this->id);
            $insert_stmt->bindParam(2, $this->links[$i]->getId());
            // Execute, catch any errors resulting
            $insert_stmt->execute();
        }
        //</editor-fold>
        //<editor-fold desc="save to multiprocess table" defaultstate="collapsed">
        // Prepare the statement
        //$this->subDataFlowDiagram->save($pdo);
        $insert_stmt = $pdo->prepare("INSERT INTO multiprocess (dfd_id, mp_id) VALUES(?,?)");

        // Bind the parameters of the prepared statement
        $insert_stmt->bindParam(1, $this->subDataFlowDiagram->getId());
        $insert_stmt->bindParam(2, $this->id);

        // Execute, catch any errors resulting
        $insert_stmt->execute();
        //</editor-fold>
    }

    //</editor-fold>
}

?>
