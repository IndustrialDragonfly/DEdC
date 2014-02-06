<?php
/**
 * The Database Storage class implements the ReadStorabel and WriteStorable
 * classes to utilize a database as a storage mechanism for DEdC
 *
 * @author eugene
 */

require_once 'ReadStorable.php';
require_once 'WriteStorable.php';

class DatabaseStorage implements ReadStorable, WriteStorable
{
    protected $dbh;
            
    public function __construct()
    {
        // The variables like username, hostname, etc need to move into a config
        // file later, here for convience for now
        // Setup for the database for the PDO object to use.
        $db_type = 'mysql';
        //$db_type = 'postgres';
        $db_hostname = 'localhost';
        $db_database = 'dedc';
        $db_username = 'dedc_user';
        $db_password = 'dedc';
        
        if ('mysql' === $db_type)
        {
            $db_id = "mysql:host=$db_hostname;dbname=$db_database";
        }
        if ('postgres' === $db_type)
        {
            $db_id = "pgsql:host=$db_hostname;dbname=$db_database";
        }

        // DB Setup
        try
        {
            $this->dbh = new PDO($db_id, $db_username, $db_password);
        } catch (PDOException $e)
        {
            die("Failed to connect to DB" . $e->getMessage());
        }
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    /**
     * Given a resource UUID, returns its type (or throws an exception if that
     * id doesn't exist). Uses PDO to access many different SQL type databases.
     * @param String $resource
     * @return String
     * @throws BadFunctionCallException
     */
    public function getTypeFromUUID($resource)
    {
         $type_find = $this->dbh->prepare("SELECT type FROM entity WHERE id=?");
         $type_find->bindParam(1, $resource);
         $type_find->execute();
         $type = $type_find->fetch();
         if($type == FALSE )
         {
             // Should probably make this a custom exception type
             throw new BadFunctionCallException("no matching id found in entity DB");
         }
         return $type['type'];
    }
    
//<editor-fold desc="Node Related Functions" defaultstate="collapsed">
    public function saveNode($id, $label, $type, $originator, $x, $y, $links, $numLinks, $parentId)
    {
        //<editor-fold desc="save to Entity table" defaultstate="collapsed">
        // Prepare the statement
        $insert_stmt = $this->dbh->prepare("INSERT INTO entity (id, label, type, originator) VALUES(?,?,?,?)");

        // Bind the parameters of the prepared statement
        $insert_stmt->bindParam(1, $id);
        $insert_stmt->bindParam(2, $label);
        $insert_stmt->bindParam(3, $type);
        $insert_stmt->bindParam(4, $originator);

        // Execute, catch any errors resulting
        $insert_stmt->execute();
        //</editor-fold>
        //<editor-fold desc="save to Element table" defaultstate="collapsed">
        // Prepare the statement
        $insert_stmt = $this->dbh->prepare("INSERT INTO element (id, x, y) VALUES(?,?,?)");

        // Bind the parameters of the prepared statement
        $insert_stmt->bindParam(1, $id);
        $insert_stmt->bindParam(2, $x);
        $insert_stmt->bindParam(3, $y);

        // Execute, catch any errors resulting
        $insert_stmt->execute();
        //</editor-fold>
        //<editor-fold desc="save to Node table" defaultstate="collapsed">
        // Prepare the statement
        $insert_stmt = $this->dbh->prepare("INSERT INTO node (id, df_id) VALUES(?,?)");
        for ($i = 0; $i < $numLinks; $i++)
        {
            // Bind the parameters of the prepared statement
            $insert_stmt->bindParam(1, $id);
            $insert_stmt->bindParam(2, $links[$i]);
            // Execute, catch any errors resulting
            $insert_stmt->execute();
        }
        //</editor-fold>
        
        // Save into the DFD (element_list table)
        $insert_stmt = $this->dbh->prepare("INSERT INTO element_list (dfd_id, el_id) VALUES (?,?)");
        $insert_stmt->bindParam(1, $parentId);
        $insert_stmt->bindParam(2, $id);
        $insert_stmt->execute();
    }
    
    /**
     * loadNode takes as input a UUID and returns an associative array
     * of all information related to that ID from the database.
     * 
     * @param String $id
     * @return associative array
     * @throws BadFunctionCallException
     */
    public function loadNode($id)
    {
        // Get main Node information
         $load = $this->dbh->prepare("SELECT * FROM entity NATURAL JOIN element WHERE id=?");
         $load->bindParam(1, $id);
         $load->execute();
         $node_vars = $load->fetch();
         if($node_vars == FALSE )
         {
            throw new BadFunctionCallException("No matching id found in entity DB");
         }
         
         // Get links list
         $load = $this->dbh->prepare("SELECT * FROM node WHERE id=?");
         $load->bindParam(1, $id);
         $load->execute();
         
         //extract all the ids of the elements
         $df_list = array();
         $newDF = $load->fetch();
         while ($newDF != FALSE)
         {
            array_push($df_list,$newDF['df_id']);
            $newDF = $load->fetch();
         }
                  
         // Put array of all dataflow ids into array to return as links
         $node_vars['links'] = $df_list;
         
         // Setup select statement to grab parent DFD id
        $select_stmt = $this->dbh->prepare('SELECT * FROM element_list WHERE el_id = ?');
        $select_stmt->bindParam(1, $id);
        $select_stmt->execute();
        $parent =  $select_stmt->fetch();
        
        if ($parent === FALSE)
        {
            $node_vars['dfd_id'] = NULL;
        }
        else
        {
            $node_vars['dfd_id'] = $parent['dfd_id'];
        }
        
         return $node_vars;
    }
    
    /**
     * Deletes the node object passed from all relevant tables
     * 
     * @param String $id
     */
    public function deleteNode($id)
    {
        // Delete from node table
        $delete = $this->dbh->prepare("DELETE FROM node WHERE id=?");
        $delete->bindParam(1, $id);
        $delete->execute();
        
        // Delete from element table
        $delete = $this->dbh->prepare("DELETE FROM element WHERE id=?");
        $delete->bindParam(1, $id);
        $delete->execute();
        
        // Delete from entity table
        $delete = $this->dbh->prepare("DELETE FROM entity WHERE id=?");
        $delete->bindParam(1, $id);
        $delete->execute();
        
    }
    //</editor-fold>
   
    /**
     * Stores the mapping between a subDFDNode and its DFD into the database
     * 
     * @param String $dfd_resource
     * @param String $mp_resource
     */
    public function saveSubDFDNode($dfd_resource, $subDFD_resource)
    {
        //<editor-fold desc="save to multiprocess table" defaultstate="collapsed">
      // Prepare the statement
      $insert_stmt = $this->dbh->prepare("INSERT INTO subdfdnode (dfd_id, subdfdnode_id) VALUES(?,?)");

      // Bind the parameters of the prepared statement
      $insert_stmt->bindParam(1, $dfd_resource);
      $insert_stmt->bindParam(2, $subDFD_resource);

      // Execute, catch any errors resulting
      $insert_stmt->execute();
      //</editor-fold>
    }
    
    /**
     * For a given id that is of type subDFDNode, return what dfd it maps to
     * @param String $id
     * @return String
     */
    public function loadSubDFDNode($id)
    {
         $select_statement = $this->dbh->prepare("SELECT dfd_id FROM subdfdnode WHERE subdfdnode_id=?");
         $select_statement->bindParam(1, $id);
         $select_statement->execute();
         $dfd_id = $select_statement->fetch();
         
         // Return the id from the associative array
         return $dfd_id['dfd_id'];
    }
    
    /**
     * Deletes the given subDFDNode from the subDFDNode to DFD mapping
     * @param String $id
     */
    public function deleteSubDFDNode($id)
    {
        $delete_statement = $this->dbh->prepare("DELETE FROM subdfdnode WHERE subdfdnode_id=?");
        $delete_statement->bindParam(1, $id);
        $delete_statement->execute();
    }
    
    /**
     * Stores a dataflow object into the database
     * 
     * @param string $id
     * @param string $label
     * @param string $type
     * @param origin $originator
     * @param int $x
     * @param int $y
     * @param string $origin_resource
     * @param string $dest_resource
     */
    public function saveLink($id, $label, $type, $originator, $x, $y, $origin_resource, $dest_resource, $parentId)
    {
      //<editor-fold desc="save to Entity table" defaultstate="collapsed">
      // Prepare the statement
      $insert_stmt = $this->dbh->prepare("INSERT INTO entity (id, label, type, originator) VALUES(?,?,?,?)");

      // Bind the parameters of the prepared statement
      $insert_stmt->bindParam(1, $id);
      $insert_stmt->bindParam(2, $label);
      $insert_stmt->bindParam(3, $type);
      $insert_stmt->bindParam(4, $originator);

      // Execute, catch any errors resulting
      $insert_stmt->execute();
      //</editor-fold>
      
      //<editor-fold desc="save to Element table" defaultstate="collapsed">
      // Prepare the statement
      $insert_stmt = $this->dbh->prepare("INSERT INTO element (id, x, y) VALUES(?,?,?)");

      // Bind the parameters of the prepared statement
      $insert_stmt->bindParam(1, $id);
      $insert_stmt->bindParam(2, $x);
      $insert_stmt->bindParam(3, $y);

      // Execute, catch any errors resulting
      $insert_stmt->execute();
      //</editor-fold>
      
      //<editor-fold desc="save to link table" defaultstate="collapsed">
      // Prepare the statement
      if($origin_resource != NULL && $dest_resource != NULL)
      {
         $insert_stmt = $this->dbh->prepare("INSERT INTO link (id, origin_id, dest_id) VALUES(?,?,?)");
         // Bind the parameters of the prepared statement
         $insert_stmt->bindParam(1, $id);
         $insert_stmt->bindParam(2, $origin_resource);
         $insert_stmt->bindParam(3, $dest_resource);
         // Execute, catch any errors resulting
         $insert_stmt->execute();
      }
      else if($origin_resource == NULL && $dest_resource != NULL)
      {
         $insert_stmt = $this->dbh->prepare("INSERT INTO link (id, dest_id) VALUES(?,?)");
         // Bind the parameters of the prepared statement
         $insert_stmt->bindParam(1, $id);
         $insert_stmt->bindParam(2, $dest_resource);
         // Execute, catch any errors resulting
         $insert_stmt->execute();
      }
      else if($origin_resource != NULL && $dest_resource == NULL)
      {
         $insert_stmt = $this->dbh->prepare("INSERT INTO link (id, origin_id) VALUES(?,?)");
         // Bind the parameters of the prepared statement
         $insert_stmt->bindParam(1, $id);
         $insert_stmt->bindParam(2, $origin_resource);
         // Execute, catch any errors resulting
         $insert_stmt->execute();
      }
      if($origin_resource == NULL && $dest_resource == NULL)
      {
         $insert_stmt = $this->dbh->prepare("INSERT INTO link (id) VALUES(?)");
         // Bind the parameters of the prepared statement
         $insert_stmt->bindParam(1, $id);
         // Execute, catch any errors resulting
         $insert_stmt->execute();
      }
      //</editor-fold>
      
      // Save to dfd 
        $insert_stmt = $this->dbh->prepare("INSERT INTO element_list (dfd_id, el_id) VALUES (?,?)");
        $insert_stmt->bindParam(1, $parentId);
        $insert_stmt->bindParam(2, $id);
        $insert_stmt->execute();
    }
    
     /**
     * loadLink takes an input of a UUID and returns an associative array of
     * all the informaiton related to that ID from the database.dragonfly
     * 
     * @param string $id
     */
    public function loadLink($id)
    {
        // Setup select statement
        $select_stmt = $this->dbh->prepare('SELECT * FROM entity NATURAL JOIN element NATURAL JOIN link WHERE id = ?');
        $select_stmt->bindParam(1, $id);
        $select_stmt->execute();
        $results =  $select_stmt->fetch();
        
        // If there was no matching ID, thrown an exception
        if($results === FALSE )
         {
            throw new BadFunctionCallException("no matching id found in entity DB");
         }
         
        // Setup select statement to grab parent DFD id
        $select_stmt = $this->dbh->prepare('SELECT * FROM element_list WHERE el_id = ?');
        $select_stmt->bindParam(1, $id);
        $select_stmt->execute();
        $parent =  $select_stmt->fetch();
        
        if ($parent === FALSE)
        {
            $results['dfd_id'] = NULL;
        }
        else
        {
            $results['dfd_id'] = $parent['dfd_id'];
        }
         
         // Return the assocative array
         return $results;
    }
    
    /**
     * Deletes the link from the database.
     * 
     * @param String $id
     */
    public function deleteLink($id)
    {
        // Delete from node table
        $delete = $this->dbh->prepare("DELETE FROM link WHERE id=?");
        $delete->bindParam(1, $id);
        $delete->execute();
        
        // Delete from element table
        $delete = $this->dbh->prepare("DELETE FROM element WHERE id=?");
        $delete->bindParam(1, $id);
        $delete->execute();
        
        // Delete from entity table
        $delete = $this->dbh->prepare("DELETE FROM entity WHERE id=?");
        $delete->bindParam(1, $id);
        $delete->execute();
    }
    
    /**
     * Returns a stack of all the ancestors of the DFD whose
     * UUID it has been passed.
     * @param String $id
     * @return String[]
     */
    private function getAncestry($id)
    {
        // This should produce a stack of the ancestry, with the root at top
        // and the immediate parent the last entry
        $loadAncestry = $this->dbh->prepare("SELECT ancestor_id
            FROM dfd_ancestry 
            WHERE descendant_id=?
            ORDER BY depth DESC");
        $loadAncestry->bindParam(1, $id);
        
        // Iterate through the results until all have been pulled out
        $ancestryStack = array();
        $newId = $loadAncestry->fetch();
        while ($newId != FALSE)
         {
            array_push($ancestryStack,$newId['ancestor_id']);
            $newId = $loadAncestry->fetch();
         }
         
         // Return the whole stack
         return $ancestryStack;
    }
    /**
     * Needs updating to handle the fact its a jagged array and elementList
     * has been split up
     * Returns an associative array of the format:
     * ['id'] string
     * ['label'] string
     * ['originator'] string
     * ['elementList'] string[]
     * ['ancestry'] string[]
     * @param String $id
     * @return Mixed[]
     * @throws BadFunctionCallException
     */
    public function loadDFD($id)
    {
        // Get the id, label, originator, and curtesy of subdfdnode, the 
        // subDFDNode the DFD is linked to
         $loadDFD = $this->dbh->prepare("SELECT * 
             FROM entity id
                JOIN subdfdnode subdfdnode_id ON id=subdfdnode
             WHERE id=?");
         $loadDFD->bindParam(1, $id);
         $loadDFD->execute();
         $vars = $loadDFD->fetch();
         if($vars == FALSE )
         {
            throw new BadFunctionCallException("no matching id found in entity DB");
         }
         
         // Get the nodes list from the database
         // This is performed by joining element list, entity and element, then 
         // filtering out all subdfdnodes and links from the list with a
         // a subquery
         $loadDFD = $this->dbh->prepare("
             SELECT * 
            FROM entity id
                    JOIN element_list el_id ON el_id=id
                    NATURAL JOIN element
            WHERE id NOT IN (SELECT subdfdnode_id FROM subdfdnode UNION SELECT id FROM link) AND dfd_id=?;
                ");
         $loadDFD->bindParam(1, $id);
         $loadDFD->execute();
         
         // Extract all the data of the nodes
         $nodeList = array();
         $newNode = $loadDFD->fetch();
         while ($newNode != FALSE)
         {
            array_push($nodeList,$newNode);
            $newNode = $loadDFD->fetch();
         }
         
         // Add the data of the nodes to the $vars array that is
         // to be returned
         $vars['nodeList'] = $nodeList;
         
         
         // Get the links list from the database
         // This is performed by joining the relevant tables
         $loadDFD = $this->dbh->prepare("
             SELECT * 
                FROM link id 
                        JOIN element_list el_id ON id=el_id
                        NATURAL JOIN entity
                        NATURAL JOIN element
                        NATURAL JOIN link
                WHERE dfd_id=?;
                ");
         $loadDFD->bindParam(1, $id);
         $loadDFD->execute();
         
         // Extract all the data of the nodes
         $linkList = array();
         $newLink = $loadDFD->fetch();
         while ($newNode != FALSE)
         {
            array_push($linkList,$newLink);
            $newLink = $loadDFD->fetch();
         }
         
         // Add the data of the links to the $vars array that is
         // to be returned
         $vars['linkList'] = $linkList;
         
         
         // Get the subdfdnode list from the database
         // This is performed by joining the relevant tables
         // This first approach should work, but MySQL seems to have a bug
         // relating to this approach (something about a JOIN and having
         // to specify the column name as table.column makes MySQL assume that
         // the WHERE clause is impossible - supposedly fixed in new versions.)
         /*$loadDFD = $this->dbh->prepare("
             SELECT *
                FROM node id
                        JOIN element_list el_id ON el_id=id
                        NATURAL JOIN element
                        NATURAL JOIN entity
                        JOIN subdfdnode subdfdnode_id ON subdfdnode_id=id
                WHERE \"element_list.dfd_id\"=?;
                ");*/
         // Scarier looking work around version
         $loadDFD = $this->dbh->prepare("
             SELECT *
                FROM node id
                        NATURAL JOIN element
                        NATURAL JOIN entity
                        JOIN subdfdnode subdfdnode_id ON subdfdnode_id=id
                WHERE id IN (SELECT el_id FROM element_list WHERE dfd_id=?);
                ");
         $loadDFD->bindParam(1, $id);
         $loadDFD->execute();
         
         // Extract all the data of the nodes
         $subDFDNodeList = array();
         $newsubDFDNode = $loadDFD->fetch();
         while ($newNode != FALSE)
         {
            array_push($subDFDNodeList,$newsubDFDNode);
            $newsubDFDNode = $loadDFD->fetch();
         }
         
         // Add the data from subdfdnodes to the $vars array that is
         // to be returned
         $vars['subDFDNodeList'] = $subDFDNodeList;         
         
         // Get the stack of the DFDs ancestry from the database
         $vars['ancestry'] = $this->getAncestry($id);
         
         return $vars;
    }
    
    /**
     * Adds the ancestry stack of the current DFD into the DFD tree
     * 
     * @param string $id
     * @param string[] $ancestry
     */
    private function saveAncestry($id, $ancestry)
    {
      $insert_stmt = $this->dbh->prepare("INSERT INTO dfd_ancestry (ancestor_id, descendant_id, depth) VALUES(?,?,?)");
      
      for ($i = 0; $i < count($ancestry); $i++)
      {
          $insert_stmt->bindParam(1, $ancestry[$i]);
          $insert_stmt->bindParam(2, $id);
          $insert_stmt>-bindParam(3, $i);
          
          $insert_stmt->execute();
      }
    }
    
    /**
     * Saves the DFD with the given input parameters
     * 
     * @param string $id
     * @param string $type
     * @param string $label
     * @param string $originator
     * @param string[] $ancestry
     * @param string[] $nodeList
     * @param string[] $linkList
     * @param string[] $subDFDNodeList
     * @param string $subDFDNode
     */
    public function saveDFD($id, $type, $label, $originator, $ancestry, 
            $nodeList, $linkList, $subDFDNodeList, $subDFDNode)
    {
      // Prepare the statement
      $insert_stmt = $this->dbh->prepare("INSERT INTO entity (id, label, type, originator) VALUES(?,?,?,?)");

      // Bind the parameters of the prepared statement
      $insert_stmt->bindParam(1, $id);
      $insert_stmt->bindParam(2, $label);
      $insert_stmt->bindParam(3, $type);
      $insert_stmt->bindParam(4, $originator);

      // Execute, catch any errors resulting
      $insert_stmt->execute();
      
      // Prepare the statement to store the elements into the elmenet_list table
      $insert_stmt = $this->dbh->prepare("INSERT INTO element_list (dfd_id, el_id) VALUES(?,?)");
      
      // Save each element in the nodeList to the table if it is an array
      if (is_array($nodeList))
      {
        foreach ($nodeList as $node)
        {
           // Bind the parameters of the prepared statement
           $insert_stmt->bindParam(1, $id);
           $insert_stmt->bindParam(2, $node);
           // Execute, catch any errors resulting
           $insert_stmt->execute();
        }
      }
      
      // Save each element in the linkList to the table if it is an array
      if (is_array($nodeList))
      {
        foreach ($linkList as $link)
        {
           // Bind the parameters of the prepared statement
           $insert_stmt->bindParam(1, $id);
           $insert_stmt->bindParam(2, $link);
           // Execute, catch any errors resulting
           $insert_stmt->execute();
        }
      }
      
      // Save each element in the suDFDNodeList to the table if it isn't null
      if (is_array($nodeList))
      {
        foreach ($subDFDNodeList as $subDFDNode_id)
        {
           // Bind the parameters of the prepared statement
           $insert_stmt->bindParam(1, $id);
           $insert_stmt->bindParam(2, $subDFDNode_id);
           // Execute, catch any errors resulting
           $insert_stmt->execute();
        }
      }
          // Prepare the statement
      $insert_stmt = $this->dbh->prepare("INSERT INTO subDFDNode (subdfdnode_id, dfd_id) VALUES(?,?)");

      // Bind the parameters of the prepared statement
      $insert_stmt->bindParam(1, $subDFDNode);
      $insert_stmt->bindParam(2, $id);
      
      $this->saveAncestry($id, $ancestry);
    }
    
    /**
     * Deletes the DFD information itself from the dfd_ancestry table and the
     * entity table. Expects that the DFD has already been cleared out, so does
     * not attempt to clean out elements that it contains (since those are best
     * removed by their own functions, not by the DFD).
     * 
     * @param String $id
     */
    public function deleteDFD($id)
    {
        // Start by isolating the child from its parents
        $delete = $this->dbh->prepare("DELETE FROM dfd_ancestry WHERE descendant_id = ?");
        $delete->bindParam(1, $id);
        $delete->execute();
        
        // Removing the child itself from the DB
        $delete = $this->dbh->prepare("DELETE FROM entity WHERE id=?");
        $delete->bindParam(1, $id);
        $delete->execute();
    }
}

?>