<?php
// Setup for the database for the PDO object to use.
function getDb()
{
    $db_type = 'mysql';
    //protected static $db_type = Database::postgres;
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
?>
