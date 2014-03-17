<?php
/**
 * Abstract object for a generic object which can be included in the Diagram
 * or User data model as needed. This supports adding information like a comment
 * to an object. Can be extended to add more details not directly related to
 * DFDs, but needed for other applications, such as the order in which data
 * flows, without having to modify core DFD classes such as Node.
 *
 * @author Eugene Davis
 */
abstract class ExtraInformation
{
    //<editor-fold desc="Attributes" defaultstate="collapsed">
    /**
     * textField is the text contained in the ExtraInformation object
     * @var String
     */
    protected $textField;
    /**
     * Creator of the object (id)
     * @var String
     */
    protected $originator;
    /**
     * Creator's origanization (owner of the object) (id)
     * @var String
     */
    protected $origanization;
    //</editor-fold>
    
    public function __construct()
    {
        
    }
    
    //<editor-fold desc="Getters and Setters" defaultstate="collapsed">
    /**
     * Returns the text for the object.
     * @return String
     */
    public function getText()
    {
        return $this->textField;
    }
    
    /**
     * Returns the originator of the object (id)
     * @return String
     */
    public function getOriginator()
    {
        return $this->originator;
    }
    
    /**
     * Returns the originazation owning the object (id)
     * @return String
     */
    public function getOriganization()
    {
        return $this->origanization;
    }
    
    /**
     * Sets the textfield to the text in the argument
     * @param String $text
     * @throws BadFunctionCallException
     */
    public function setText($text)
    {
        if (is_string($text))
        {
            $this->textField = $text;
        }
        else
        {
            throw new BadFunctionCallException("Input not a string.");
        }
    }
    //</editor-fold>
}

?>
