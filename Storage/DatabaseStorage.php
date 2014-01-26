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
    public function saveNode($id, $label, $type, $originator, $x, $y, $links, $numLinks)
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
            throw new BadFunctionCallException("no matching id found in entity DB");
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
      $insert_stmt = $this->dbh->prepare("INSERT INTO multiprocess (dfd_id, mp_id) VALUES(?,?)");

      // Bind the parameters of the prepared statement
      $insert_stmt->bindParam(1, $dfd_resource);
      $insert_stmt->bindParam(2, $subDFD_resource);

      // Execute, catch any errors resulting
      $insert_stmt->execute();
      //</editor-fold>
    }
    
    /**
     * Stores a dataflow object into the database
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
    public function saveLink($resource, $label, $type, $originator, $x, $y, $origin_resource, $dest_resource)
    {
      //<editor-fold desc="save to Entity table" defaultstate="collapsed">
      // Prepare the statement
      $insert_stmt = $this->dbh->prepare("INSERT INTO entity (id, label, type, originator) VALUES(?,?,?,?)");

      // Bind the parameters of the prepared statement
      $insert_stmt->bindParam(1, $resource);
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
      $insert_stmt->bindParam(1, $resource);
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
         $insert_stmt->bindParam(1, $resource);
         $insert_stmt->bindParam(2, $origin_resource);
         $insert_stmt->bindParam(3, $dest_resource);
         // Execute, catch any errors resulting
         $insert_stmt->execute();
      }
      else if($origin_resource == NULL && $dest_resource != NULL)
      {
         $insert_stmt = $this->dbh->prepare("INSERT INTO link (id, dest_id) VALUES(?,?)");
         // Bind the parameters of the prepared statement
         $insert_stmt->bindParam(1, $resource);
         $insert_stmt->bindParam(2, $dest_resource);
         // Execute, catch any errors resulting
         $insert_stmt->execute();
      }
      else if($origin_resource != NULL && $dest_resource == NULL)
      {
         $insert_stmt = $this->dbh->prepare("INSERT INTO link (id, origin_id) VALUES(?,?)");
         // Bind the parameters of the prepared statement
         $insert_stmt->bindParam(1, $resource);
         $insert_stmt->bindParam(2, $origin_resource);
         // Execute, catch any errors resulting
         $insert_stmt->execute();
      }
      if($origin_resource == NULL && $dest_resource == NULL)
      {
         $insert_stmt = $this->dbh->prepare("INSERT INTO link (id) VALUES(?)");
         // Bind the parameters of the prepared statement
         $insert_stmt->bindParam(1, $resource);
         // Execute, catch any errors resulting
         $insert_stmt->execute();
      }
      //</editor-fold>
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
        $load->bindParam(1, $this->getId());
        $load->execute();
        
        // Delete from element table
        $delete = $this->dbh->prepare("DELETE FROM element WHERE id=?");
        $load->bindParam(1, $this->getId());
        $load->execute();
        
        // Delete from entity table
        $delete = $this->dbh->prepare("DELETE FROM entity WHERE id=?");
        $load->bindParam(1, $this->getId());
        $load->execute();
    }
}

?>