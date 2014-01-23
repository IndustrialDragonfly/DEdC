<?php
/**
 * The Database Storage class implements the ReadStorabel and WriteStorable
 * classes to utilize a database as a storage mechanism for DEdC
 *
 * @author eugene
 */

require_once 'ReadStorable.php';
require_once 'WriteStorable.php';
require_once 'DatabaseStorageConfig.php';

class DatabaseStorage implements ReadStorable, WriteStorable
{
    /**
     * Given a resource UUID, returns its type (or throws an exception if that
     * id doesn't exist). Uses PDO to access many different SQL type databases.
     * @param String $resource
     * @return String
     * @throws BadFunctionCallException
     */
    public function getTypeFromUUID($resource)
    {
         $dbh = getDb();
         $type_find = $dbh->prepare("SELECT type_name FROM entity NATURAL JOIN types WHERE id=?");
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
}

?>
