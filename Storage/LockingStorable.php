<?php
/**
 * 
 * @author Jacob Swanson/Eugene Davis
 */
interface LockingStorable extends ReadStorable, WriteStorable
{
    /**
     * Check if an Entity is locked
     * @param ID $id
     */
    public function isLocked($id);
    
    /**
     * Lock an Entity
     * @param ID $id
     */
    public function setLock($id);
    
    /**
     * Release a lock on an Entity
     * @param ID $id
     */
    public function releaseLock($id);
}

?>
