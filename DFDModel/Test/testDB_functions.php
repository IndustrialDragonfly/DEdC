<?php

/**
 * Description of testDB_functions
 *
 * @author Josh Clark
 */
require_once "../Constants.php";

class testDB_functions
{

    // Comment and uncomment the relevant ones for your prefered RDMS
    protected static $db_type = Database::mysql;
    //protected static $db_type = Database::postgres;
    
    public function getConnection()
    {
        $db_hostname = 'localhost';
        $db_database = 'dedc';
        $db_username = 'tester';
        $db_password = 'test';

        if (Database::mysql === self::$db_type)
        {
            $db_id = "mysql:host=$db_hostname;dbname=$db_database";
        }
        if (Database::postgres === self::$db_type)
        {
            $db_id = "pgsql:host=$db_hostname;dbname=$db_database";
        }

        // DB Setup
        $dbh;
        try
        {
            $dbh = new PDO($db_id, $db_username, $db_password);
        } catch (PDOException $e)
        {
            die("Failed to connect to DB" . $e->getMessage());
        }
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbh;
    }

    public function resetDB($pdo)
    {
        if ($pdo instanceof PDO)
        {
            $pdo->query('BEGIN');
            $pdo->query('TRUNCATE TABLE element;');
            $pdo->query('TRUNCATE TABLE link;');
            $pdo->query('TRUNCATE TABLE node;');
            $pdo->query('TRUNCATE TABLE element_list;');
            $pdo->query('TRUNCATE TABLE subdfdnode;');
            $pdo->query('TRUNCATE TABLE dfd_ancestry;');

            // Truncate the foreign key restricted entity table in mysql
            if (Database::mysql === self::$db_type)
            {
                $pdo->query('SET foreign_key_checks=0;');
                $pdo->query('TRUNCATE TABLE entity;');
                $pdo->query('SET foreign_key_checks=1;');
            }

            // Truncate the foreign key restricted table in postgres
            if (Database::postgres === self::$db_type)
            {
                $pdo->query('TRUNCATE TABLE entity CASCADE;');
            }
            $pdo->query('COMMIT');
        } else
        {
            throw new BadFunctionCallException("input parameter was not a PDO");
        }
    }

}

?>
