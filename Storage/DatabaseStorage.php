<?php
/**
 * The Database Storage class implements the ReadStorabel and WriteStorable
 * classes to utilize a database as a storage mechanism for DEdC
 *
 * @author eugene
 */

require_once 'ReadStorable.php';
require_once 'WriteStorable.php';

// TODO: Catch PDO errors and rethrow exceptions

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
    
    //<editor-fold desc="DFD Model" defaultstate="collapsed">
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
    
    /**
     * Returns a list of of a given type by the given type from the datastore
     * 
     * @param String $type
     * @return String[]
     * @throws BadFunctionCallException
     */
    public function getListByType($type)
    {
        // TODO: Check for valid type (maybe)
        
        $selectStatement = $this->dbh->prepare("SELECT id, label FROM entity WHERE type=?");
        $selectStatement->bindParam(1, $type);
        $selectStatement->execute();
        
        $elementsArray = array();
        // PDO::FETCH_ASSOC means we only get the associative array, not the associative array and normal array
        $elementsArray['list'] = $selectStatement->fetchAll(PDO::FETCH_ASSOC);
        
        // Needs to be added manually
        $elementsArray['genericType'] = 'List';
        $elementsArray['listType'] = $type;
                
        return $elementsArray;
    }
    
    /**
     * Returns a list of types from the datastore
     * 
     * @return String[]
     */
    public function getTypes()
    {
        $selectStatement = $this->dbh->prepare("SELECT type FROM types");
        
        $typesArray = $selectStatement->fetchAll();
        
        return $typesArray;
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
        $insert_stmt = $this->dbh->prepare("INSERT INTO node (id, linkId) VALUES(?,?)");
        for ($i = 0; $i < $numLinks; $i++)
        {
            // Bind the parameters of the prepared statement
            $insert_stmt->bindParam(1, $id);
            //TODO - links should only be passing id
            //$insert_stmt->bindParam(2, $links[$i]);
            $insert_stmt->bindParam(2, $links[$i]['id']);
            // Execute, catch any errors resulting
            $insert_stmt->execute();
        }
        //</editor-fold>
        
        // Save into the DFD (element_list table)
        $insert_stmt = $this->dbh->prepare("INSERT INTO element_list (diagramId, elementId) VALUES (?,?)");
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
         $node_vars = $load->fetch(PDO::FETCH_ASSOC);
         if($node_vars == FALSE )
         {
            throw new BadFunctionCallException("No matching id found in entity DB");
         }
         
         // Get links list including their name and id
         $load = $this->dbh->prepare("
             SELECT id, label
             FROM entity
             WHERE id in (SELECT linkId FROM node WHERE id=?)");
         $load->bindParam(1, $id);
         $load->execute();
         
         //extract all the ids of the elements
         $linkList = $load->fetchAll(PDO::FETCH_ASSOC);
         
         // Put array of all dataflow ids into array to return as links
         $node_vars['linkList'] = $linkList;
         
         // Setup select statement to grab parent DFD id
        $select_stmt = $this->dbh->prepare('SELECT * FROM element_list WHERE elementId = ?');
        $select_stmt->bindParam(1, $id);
        $select_stmt->execute();
        $parent =  $select_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($parent === FALSE)
        {
            $node_vars['diagramId'] = NULL;
        }
        else
        {
            $node_vars['diagramId'] = $parent['diagramId'];
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
     * Stores the mapping between a diaNode and its DFD into the database
     * 
     * @param String $dfd_resource
     * @param String $mp_resource
     */
    public function saveDiaNode($diagramId, $diaNodeId)
    {
        //<editor-fold desc="save to diaNode table" defaultstate="collapsed">
      // Prepare the statement
      $insert_stmt = $this->dbh->prepare("INSERT INTO dianode (childDiagramId, diaNodeId) VALUES(?,?)");

      // Bind the parameters of the prepared statement
      $insert_stmt->bindParam(1, $diagramId);
      $insert_stmt->bindParam(2, $diaNodeId);

      // Execute, catch any errors resulting
      $insert_stmt->execute();
      //</editor-fold>
    }
    
    /**
     * For a given id that is of type diaNode, return what dfd it maps to
     * @param String $id
     * @return String
     * TODO - this can probably be done with load database accesses but i couldnt determine what the correct join was
     */
    public function loadDiaNode($id)
    {
        //load all of the node attributes
        $node_vars = $this->loadNode($id);
        
        //get the childDiagramId
        $select_statement = $this->dbh->prepare("SELECT childDiagramId FROM dianode WHERE diaNodeId=?");
        $select_statement->bindParam(1, $id);
        $select_statement->execute();
        $diagramId = $select_statement->fetch();
         
        // append the child id to the associative array of attributes
        $node_vars['childDiagramId'] = $diagramId['childDiagramId'];
        return $node_vars;
    }
    
    /**
     * Deletes the given diaNode from the diaNode to DFD mapping
     * @param String $id
     */
    public function deleteDiaNode($id)
    {
        $delete_statement = $this->dbh->prepare("DELETE FROM dianode WHERE diaNodeId=?");
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
         $insert_stmt = $this->dbh->prepare("INSERT INTO link (id, originNode, destinationNode) VALUES(?,?,?)");
         // Bind the parameters of the prepared statement
         $insert_stmt->bindParam(1, $id);
         $insert_stmt->bindParam(2, $origin_resource);
         $insert_stmt->bindParam(3, $dest_resource);
         // Execute, catch any errors resulting
         $insert_stmt->execute();
      }
      else if($origin_resource == NULL && $dest_resource != NULL)
      {
         $insert_stmt = $this->dbh->prepare("INSERT INTO link (id, destinationNode) VALUES(?,?)");
         // Bind the parameters of the prepared statement
         $insert_stmt->bindParam(1, $id);
         $insert_stmt->bindParam(2, $dest_resource);
         // Execute, catch any errors resulting
         $insert_stmt->execute();
      }
      else if($origin_resource != NULL && $dest_resource == NULL)
      {
         $insert_stmt = $this->dbh->prepare("INSERT INTO link (id, originNode) VALUES(?,?)");
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
        $insert_stmt = $this->dbh->prepare("INSERT INTO element_list (diagramId, elementId) VALUES (?,?)");
        $insert_stmt->bindParam(1, $parentId);
        $insert_stmt->bindParam(2, $id);
        $insert_stmt->execute();
    }
    
     /**
     * loadLink takes an input of a UUID and returns an associative array of
     * all the information related to that ID from the database.
     * 
     * @param string $id
     */
    public function loadLink($id)
    {
        // Setup select statement
        $select_stmt = $this->dbh->prepare('SELECT * FROM entity NATURAL JOIN element WHERE id = ?');
        $select_stmt->bindParam(1, $id);
        $select_stmt->execute();
        $results =  $select_stmt->fetch(PDO::FETCH_ASSOC);
        
        // If there was no matching ID, thrown an exception
        if($results === FALSE )
         {
            throw new BadFunctionCallException("No matching id found in entity DB");
         }
         
        // Setup select statement to grab parent DFD id
        $select_stmt = $this->dbh->prepare('SELECT * FROM element_list WHERE elementId = ?');
        $select_stmt->bindParam(1, $id);
        $select_stmt->execute();
        $parent =  $select_stmt->fetch(PDO::FETCH_ASSOC);
         
        // If there was no matching ID, thrown an exception
        if($parent === FALSE )
         {
            throw new BadFunctionCallException("No matching id found in elementList");
         }
        
        $results['diagramId'] = $parent['diagramId'];
        
        // Setup select statement to grab origin node info
        $select_stmt = $this->dbh->prepare('
            SELECT id, label
            FROM entity
            WHERE id IN (SELECT originNode FROM link WHERE id=?)
            ');
        $select_stmt->bindParam(1, $id);
        $select_stmt->execute();
        $originNode =  $select_stmt->fetch(PDO::FETCH_ASSOC);
        
        //if the orgin is set set it otherwise set that field to null
        if($originNode === FALSE )
         {
            //throw new BadFunctionCallException("No matching id found in link");
            $results['originNode'] = NULL;
         }
         else
         {
            $results['originNode'] = $originNode;
         }
        
        
        // Setup select statement to grab destination node info
        $select_stmt = $this->dbh->prepare('
            SELECT id, label
            FROM entity
            WHERE id IN (SELECT destinationNode FROM link WHERE id=?)
            ');
        $select_stmt->bindParam(1, $id);
        $select_stmt->execute();
        $destNode =  $select_stmt->fetch(PDO::FETCH_ASSOC);
        
        //if there is no destination node set it to null otherwise set it
        if($destNode === FALSE )
         {
            //throw new BadFunctionCallException("No matching id found in link");
            $results['destinationNode'] = NULL;
         }
         else
         {
            $results['destinationNode'] = $destNode;
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
        $loadAncestry = $this->dbh->prepare("SELECT ancestorId
            FROM dfd_ancestry 
            WHERE descendantId=?
            ORDER BY depth DESC");
        $loadAncestry->bindParam(1, $id);
        $loadAncestry->execute();
        
        // Iterate through the results until all have been pulled out
        $ancestryStack = array();
        $newId = $loadAncestry->fetch();
        while ($newId != FALSE)
         {
            array_push($ancestryStack,$newId['ancestorId']);
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
    public function loadDiagram($id)
    {
        // Get the id, label, originator, and curtesy of diaNode, the 
        // diaNode the DFD is linked to
         $loadDiagram = $this->dbh->prepare("
             SELECT id, label, originator, diaNodeId, type 
             FROM entity id
                JOIN dianode childDiagramId ON id=childDiagramId
             WHERE id=?");
         $loadDiagram->bindParam(1, $id);
         $loadDiagram->execute();
         $vars = $loadDiagram->fetch(PDO::FETCH_ASSOC);
         
         // Must be a root DFD, try without the diaNode table
         if($vars == FALSE )
         {
             $loadDiagram = $this->dbh->prepare("
                SELECT id, label, originator, type
                FROM entity id
                WHERE id=?");
            $loadDiagram->bindParam(1, $id);
            $loadDiagram->execute();
            $vars = $loadDiagram->fetch(PDO::FETCH_ASSOC);
            if($vars == FALSE )
            {
                throw new BadFunctionCallException("no matching id found in entity DB");
            }
         }
         
         // Get the nodes list from the database
         // This is performed by joining element list, entity and element, then 
         // filtering out all diaNodes and links from the list with a
         // a subquery
         $loadDiagram = $this->dbh->prepare("
            SELECT id, label, type, x, y
            FROM entity id
                    JOIN element_list elementId ON elementId=id
                    NATURAL JOIN element
            WHERE id NOT IN (SELECT diaNodeId FROM dianode UNION SELECT id FROM link) AND diagramId=?;
                ");
         $loadDiagram->bindParam(1, $id);
         $loadDiagram->execute();
         
         // Extract all the data of the nodes
         $nodeList = $loadDiagram->fetchAll(PDO::FETCH_ASSOC);

         // Add the data of the nodes to the $vars array that is
         // to be returned
         $vars['nodeList'] = $nodeList;
         
         
         // Get the links list from the database
         // This is performed by joining the relevant tables
         $loadDiagram = $this->dbh->prepare("
             SELECT id, label, type, originNode, destinationNode  
                FROM entity id 
                        JOIN element_list elementId ON id=elementId 
                        NATURAL JOIN link 
                WHERE diagramId=?;
                ");
         $loadDiagram->bindParam(1, $id);
         $loadDiagram->execute();
         
         // Extract all the data of the nodes
         $linkList = $loadDiagram->fetchAll(PDO::FETCH_ASSOC);
         
         // Add the data of the links to the $vars array that is
         // to be returned
         $vars['linkList'] = $linkList;
         
         
         // Get the diaNode list from the database
         // This is performed by joining the relevant tables
         // This first approach should work, but MySQL seems to have a bug
         // relating to this approach (something about a JOIN and having
         // to specify the column name as table.column makes MySQL assume that
         // the WHERE clause is impossible - supposedly fixed in new versions.)
         /*$loadDiagram = $this->dbh->prepare("
             SELECT *
                FROM node id
                        JOIN element_list elementId ON elementId=id
                        NATURAL JOIN element
                        NATURAL JOIN entity
                        JOIN dianode diaNodeId ON diaNodeId=id
                WHERE \"element_list.diagramId\"=?;
                ");*/
         // Scarier looking work around version
         $loadDiagram = $this->dbh->prepare("
             SELECT id, label, type, childDiagramId, x, y
                FROM entity id
                        NATURAL JOIN element
                        JOIN dianode diaNodeId ON diaNodeId=id
                WHERE id IN (SELECT elementId FROM element_list WHERE diagramId=?);
                ");
         $loadDiagram->bindParam(1, $id);
         $loadDiagram->execute();
         
         // Extract all the data of the nodes
         $DiaNodeList = $loadDiagram->fetchAll(PDO::FETCH_ASSOC);
         
         // Add the data from diaNodes to the $vars array that is
         // to be returned
         $vars['DiaNodeList'] = $DiaNodeList;         
         
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
      $insert_stmt = $this->dbh->prepare("INSERT INTO dfd_ancestry (ancestorId, descendantId, depth) VALUES(?,?,?)");
      
      for ($i = 0; $i < count($ancestry); $i++)
      {
          $insert_stmt->bindParam(1, $ancestry[$i]);
          $insert_stmt->bindParam(2, $id);
          $insert_stmt->bindParam(3, $i);
          
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
     * @param string[] $DiaNodeList
     * @param string $diaNode
     */
    public function saveDiagram($id, $type, $label, $originator, $ancestry, 
            $nodeList, $linkList, $DiaNodeList, $diaNode)
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
      $insert_stmt = $this->dbh->prepare("INSERT INTO element_list (diagramId, elementId) VALUES(?,?)");
      
      // Save each element in the nodeList to the table if it is an array
      if (is_array($nodeList))
      {
        foreach ($nodeList as $node)
        {
           // Bind the parameters of the prepared statement
           $insert_stmt->bindParam(1, $id);
           $insert_stmt->bindParam(2, $node['id']);
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
           $insert_stmt->bindParam(2, $link['id']);
           // Execute, catch any errors resulting
           $insert_stmt->execute();
        }
      }
      
      // Save each element in the subDiaNodeList to the table if it isn't null
      if (is_array($DiaNodeList))
      {
        foreach ($DiaNodeList as $diaNodeId)
        {
           // Bind the parameters of the prepared statement
           $insert_stmt->bindParam(1, $id);
           $insert_stmt->bindParam(2, $diaNodeId['id']);
           // Execute, catch any errors resulting
           $insert_stmt->execute();
        }
      }
          // Prepare the statement
      $insert_stmt = $this->dbh->prepare("INSERT INTO dianode (diaNodeId, childDiagramId) VALUES(?,?)");

      // Bind the parameters of the prepared statement
      $insert_stmt->bindParam(1, $diaNode);
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
    public function deleteDiagram($id)
    {
        // Start by isolating the child from its parents
        $delete = $this->dbh->prepare("DELETE FROM dfd_ancestry WHERE descendantId = ?");
        $delete->bindParam(1, $id);
        $delete->execute();
        
        // Removing the child itself from the DB
        $delete = $this->dbh->prepare("DELETE FROM entity WHERE id=?");
        $delete->bindParam(1, $id);
        $delete->execute();
    }
    
        //</editor-fold>

    //<editor-fold desc="User Model" defaultstate="collapsed">
    
    /**
     * Save a User to the database
     * @param String $userName
     * @param String $id
     * @param String $organization
     * @param Bool $admin
     */
    public function saveUser($id, $userName, $organization, $admin)
    {
        // Prepare the insert statement
        $insert_stmt = $this->dbh->prepare(
                "INSERT
                INTO users (id, userName, organization, admin)
                VALUES(?,?,?,?)"
                );
        
        // Bind the parameters
        $insert_stmt->bindParam(1, $id);
        $insert_stmt->bindParam(2, $userName);
        $insert_stmt->bindParam(3, $organization);
        $insert_stmt->bindParam(4, $admin);

        $insert_stmt->execute();
    }
    
    /**
     * Get a User's id
     * @param String userName
     * @param String organization
     * @return String
     */
    public function getUserId($userName, $organization)
    {
        // Given userName and organization
        $loadUser = $this->dbh->prepare("
                SELECT id
                FROM users
                WHERE userName=? AND organization=?"
                );
        $loadUser->bindParam(1, $userName);
        $loadUser->bindParam(2, $organization);

        $loadUser->execute();

        $results = $loadUser->fetch(PDO::FETCH_ASSOC);

        return $results["id"];
    }
    
    /**
     * Load a User from the database using id
     * @param String id
     * @return String[] userName and organization
     */
    public function loadUser($id)
    {
        // Given id
        $loadUser = $this->dbh->prepare(
                "SELECT userName, organization
                FROM  users
                WHERE id=?"
                );
        $loadUser->bindParam(1, $id);

        $loadUser->execute();

        // Get results
        $results =  $loadUser->fetch(PDO::FETCH_ASSOC);

        // If there was no matching id, thrown an exception
        if($results === FALSE )
        {
            throw new BadFunctionCallException("No user with given id found in the database.");
        }

        return $results;
    }
    
    // TODO: Look at moving the hash related programs to a separate DB access class
    /**
       $hash = $this->storage->getHash($this->id);
     * @param String $id
     * @return String Hash
     * @throws BadFunctionCallException
     */
    public function getHash($id)
    {
        // Load a hash using a user's id
        $loadHash = $this->dbh->prepare(
                "SELECT hash "
                . "FROM hash "
                . "WHERE id=?"
                );
        $loadHash->bindParam(1, $id);
        
        $loadHash->execute();
        
        // Only one row should be returned
        if ($loadHash->rowCount() != 1) 
        {
            throw new BadConstructorCallException("Multiple hashes should not be fetched.");
        }

        $result = $loadHash->fetch();        
        return $result[0];
    }
  
    /**
     * Update or save the hash
     * @param String $id
     * @param String hash
     */
    public function saveHash($id, $hash)
    {
       $checkHash = $this->dbh->prepare(
               "SELECT id "
               . "FROM hash "
               . "WHERE id=?"
               );
       $checkHash->bindParam(1, $id);
       $checkHash->execute();
       
       // If it exists
       if ($checkHash->rowCount() == 1)
       {
            $saveHash = $this->dbh->prepare(
                    "UPDATE hash "
                    . "SET hash=?"
                    . "WHERE id=?"
                    );
            $saveHash->bindParam(1, $hash);
            $saveHash->bindParam(2, $id);

            // TODO: Wrap with catch statement to convert PDO exception to storage
            $saveHash->execute();        
       }
       
       // If it doesn't exist yet'
       elseif ($checkHash->rowCount() == 0)
       {
           $saveHash = $this->dbh->prepare(
                   "INSERT "
                   . "INTO hash (id, hash) "
                   . "VALUES (?, ?)");
           $saveHash->bindParam(1, $id);
           $saveHash->bindParam(2, $hash);
           
           // TODO: Wrap with catch statement to convert any PDO exception to a storage one
           $saveHash->execute();
       }
       
       // Something very wrong in the DB
       else
       {
           // TDOO: Throw exception
       }
           
    }
    
    //</editor-fold>

}

?>