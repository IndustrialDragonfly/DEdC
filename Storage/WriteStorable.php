<?php
/**
 * The WriteStorable interface defines the functions that a write storage class
 * must implement for use with DEdC. Its counterpart is the ReadStorable
 * class, which will define the functions required for a read storage class.
 * It is not required that both interfaces be implemented, allowing for the 
 * creation of write or read only media, such as for exporting to formats
 * like PDF
 * @author eugene
 */
interface WriteStorable
{
    /**
     * Saves a given node object into the data store
     * 
     * @param String $resource
     * @param String $label
     * @param String $type
     * @param String $originator
     * @param int $x
     * @param int $y
     * @param String array $links
     */
    public function saveNode($resource, $label, $type, $originator, $x, $y, $links, $numLinks, $parentId);
    
    /**
     * Deletes the node ID passed in from the data store
     * 
     * @param String $id
     */
    public function deleteNode($id);
    
    /**
     * Stores the mapping between a subDFDNode and its DFD into the data store
     * 
     * @param String $dfd_resource
     * @param String $mp_resource
     */
    public function saveSubDFDNode($dfd_resource, $subDFD_resource);
    
     /**
     * Deletes the given subDFDNode from the subDFDNode to DFD mapping
     * @param String $id
     */
    public function deleteSubDFDNode($id);
    
    /**
     * Stores a dataflow object into the data store
     * 
     * @param string $resource
     * @param string $label
     * @param string $type
     * @param origin $originator
     * @param int $x
     * @param int $y
     * @param string $origin_resource
     * @param string $dest_resource
     */
    public function saveLink($resource, $label, $type, $originator, $x, $y, $origin_resource, $dest_resource, $parentId);
    
    /**
     * Deletes the link from the data store.
     * 
     * @param String $id
     */
    public function deleteLink($id);
    
    /**
     * Saves the DFD to the database
     */
   public function saveDFD($id, $type, $label, $originator, $ancestry, 
            $nodeList, $linkList, $subDFDNodeList, $subDFDNode);   
    /**
     * Deletes the DFD from the database
     */
    public function deleteDFD($id);
}

?>