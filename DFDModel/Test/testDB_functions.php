<?php
/**
 * Description of testDB_functions
 *
 * @author Josh Clark
 */
class testDB_functions
{
   public function getConnection()
   {
      $db_hostname = 'localhost';
      $db_database = 'dedc';
      $db_username = 'tester';
      $db_password = 'test';

      // Combined driver/host/db string
      // Comment and uncomment the relevant ones for your prefered RDMS
      $db_id = "mysql:host=$db_hostname;dbname=$db_database";
      //$db_id = "pgsql:host=$db_hostname;dbname=$db_database";

      // DB Setup
      $dbh;
      try 
      {
          $dbh = new PDO($db_id, $db_username, $db_password);
      }
      catch (PDOException $e)
      {
          die("Failed to connect to DB" . $e->getMessage());
      }
      $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
      return $dbh;
   }
   
   public function resetDB($pdo)
   {
      if( $pdo instanceof PDO)
      {
         $pdo->query('USE dedc;');
         $pdo->query('SET foreign_key_checks=0;');
         $pdo->query('TRUNCATE TABLE entity;');
         $pdo->query('TRUNCATE TABLE element;');
         $pdo->query('TRUNCATE TABLE dataflow;');
         $pdo->query('TRUNCATE TABLE node;');
         $pdo->query('TRUNCATE TABLE external_links;');
         $pdo->query('TRUNCATE TABLE element_list;');
         $pdo->query('TRUNCATE TABLE multiprocess;');
         $pdo->query('SET foreign_key_checks=1;');
      }
      else 
      {
         throw new BadFunctionCallException("input parameter was not a PDO");
      }
   }
}
?>
