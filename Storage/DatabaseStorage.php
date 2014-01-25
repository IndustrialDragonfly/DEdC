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
        // $db_type = postgres;
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
         $type_find = $this->dbh->prepare("SELECT type_name FROM entity NATURAL JOIN types WHERE id=?");
         $type_find->bindParam(1, $resource);
         $type_find->execute();
         $type = $type_find->fetch();
         if($type == FALSE )
         {
             // Should probably make this a custom exception type
             throw new BadFunctionCallException("no matching id found in entity DB");
         }
         return $type['type_name'];
    }
    
    public function saveNode($id, $label, $type, $originator, $x, $y, $links, $numLinks)
    {
        //<editor-fold desc="save to Entity table" defaultstate="collapsed">
        // Prepare the statement
        $insert_stmt = $this->dbh->prepare("INSERT INTO entity (id, label, type, originator) VALUES(?,?,?,?)");

        // Bind the parameters of the prepared statement
        $type = Types::Process;
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
            $insert_stmt->bindParam(2, $links[$i]->getId());
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
         $mySQLstatement = $this->dbh->prepare("SELECT * FROM entity NATURAL JOIN element WHERE id=?");
         $mySQLstatement->bindParam(1, $id);
         $mySQLstatement->execute();
         $node_vars = $mySQLstatement->fetch();
         if($node_vars == FALSE )
         {
            throw new BadFunctionCallException("no matching id found in entity DB");
         }
         return $node_vars;
    }
}

?>