<?php

/**
 * ID can either be newly generated, or instantiated using an existing ID
 *
 * @author Jacob Swanson/Eugene Davis
 */
class ID {
    /**
     * @var String 
     */
    private $id = "";
    
    /**
     * @var String 
     */
    private $uuidTag = "_id";
    
    /**
     * Create a new ID object
     * @param String $id (optional)
     * @throws BadConstructorCallException
     */
    public function __construct()
    {
        // Generate a new ID
        if (func_num_args() == 0)
        {
            $this->id = $this->generateId();
        }
        // Given an id with or without a uuid tag
        else if (func_num_args() == 1)
        {
            // Get the location of the uuid tag
            $tagPos = stripos(func_get_arg(0), $this->uuidTag);
            
            // If the tag was not found
            if ($tagPos === FALSE)
            {
                // Received just an id
                $this->id = func_get_arg(0);
            }
            // If the tag was found
            else
            {
                // Received an id with an appended tag
                // Remove the tag, and set id
                $id = substr(func_get_arg(0), 0, $tagPos);
                $this->id = $id;
            }
        }
        else
        {
            throw new BadConstructorCallException(
                    "ID constructor takes the argument of a single id (with or "
                    . "without a tag) or nothing."
                    );
        }
    }
    
    /**
     * Get the id without the uuid tag
     * @return String
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Get the id with the uuid tag
     * @return String
     */
    public function getTaggedId()
    {
        return $this->id . $this->uuidTag;
    }
    
    /**
     * This is a function that generates a UUID String with a length of 265 bits
     * @return String
     */
    private function generateId()
    {
        $length = 256;
        $numberOfBytes = $length / 8;
        // Replaces all instances of +, = or / in the Base64 string with x
        return str_replace(array("+", "=", "/"), array("x", "x", "x"), base64_encode(openssl_random_pseudo_bytes($numberOfBytes)));
    }
}
