<?php
/**
 * The Requestable interface specifies functions that all request objects no
 * matter their HTTP method type must implement.
 * @author eugene
 */
interface Requestable
{
    /**
     * Returns the UUID of the element that the client wishes to access.
     * Can return NULL if a path down the ancestry tree is provided.
     * 
     * @returns String
     */
    public function getId();
    
     /**
     * Returns the path (down the ancestry tree) from a root DFD to a particular
     * element, according to the client. Can be NULL if an ID is provided.
     * 
     * @returns String
     */
    public function getResource();
    
    /**
     * Returns the acceptable media types in an array
     * 
     * @returns String[]
     */
    public function getAcceptTypes();
    
    /**
     * Returns the HTTP method being used.
     * 
     * @returns MethodsEnum
     */
    public function getMethod();
}