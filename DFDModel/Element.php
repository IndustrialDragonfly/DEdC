<?php

require_once 'Entity.php';
/**
 * this class reresents an abstract object in a DFD which has a everything in an 
 * Enity in addition to a X-Y coordinate and a parent DFD 
 * 
 * known direct subclasses:
 *    Node
 *    Link
 * 
 * inherits from Entity
 *
 * @author Josh Clark
 */
abstract class Element extends Entity
{
    //<editor-fold desc="Attributes" defaultstate="collapsed">
    /**
     * the x coordinate of this element
     * @var int
     */
    protected $x;

    /**
     * the y coordinate of this element
     * @var int
     */
    protected $y;

    /**
     * the UUID of the parent DFD that contains this element
     * @var type String
     */
    protected $parent;

    //</editor-fold>
    //<editor-fold desc="Constructor" defaultstate="collapsed">
    /**
     *This is a constructor for the Element class.  This function always takes 
     * in 2 parameters, the first is always a valid storage medium and will be 
     * handled by the constructor for Entity.  the second paramenter will be 
     * either the UUID of the parent Diagram or will be an associative array.  
     * If the second parameter is an UUID the parent Diagram is set and this is 
     * treated like a null constructor, and will create an object with default 
     * values.  If the second parameter is an associative array it is passed to 
     * the Entity constructor for it to handle.
     * @param {ReadStorable,WriteStorable} $datastore
     * @param string $id the UUID of the parent Diagram
     * @param Mixed[] $assocativeArray
     */
    public function __construct()
    {
        if(func_num_args() == 2)
        {
            //if the second parameter is an ID
            if(is_string(func_get_arg(1)))
            {
                parent::__construct(func_get_arg(0));
                $this->x = 0;
                $this->y = 0;
                // Find if the type of the second argument is an id of Diagram, if so, its a new node
                $type = $this->storage->getTypeFromUUID(func_get_arg(1));
                if (is_subclass_of($type, "Diagram"))
                {
                    $this->parent = func_get_arg(1);
                }
                else
                {
                    throw new BadConstructorCallException("The Id passed to the Element constructor was not valid Diagram");
                }
            }
            //if the second parmeter as an associative array pass it along to the Entity constructor
            else if(is_array(func_get_arg(1)))
            {
                parent::__construct(func_get_arg(0), func_get_arg(1));
            }
            //if the second paramenter wasnt an ID or an array throw an exception
            else
            {
                throw new BadConstructorCallException("Invalid second parameter passed to Element constructor, can be either an ID or an assocative array");
            }
            
        }
        else
        {
            throw new BadConstructorCallException("An incorrect number of parameters were passed to the Element constructor");
        }
        
    }

    //</editor-fold>
    //<editor-fold desc="Accessor functions" defaultstate="collapsed">
    //<editor-fold desc="X Y Accessors" defaultstate="collapsed">
    /**
     * A function that sets the x coordinate of this element
     * @param int $newX
     */
    public function setX($newX)
    {
        $this->x = $newX;
    }

    /**
     * A function that returns the X coordinate of this element
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * A function that sets the Y coordinate of this element
     * @param int $newY
     */
    public function setY($newY)
    {
        $this->y = $newY;
    }

    /**
     * a function that returns the Y coordinate of this element
     * @return type
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * function that sets both the X and Y values at the same time
     * @param type $newX the X value to be set
     * @param type $newY the Y value to be set
     */
    public function setLocation($newX, $newY)
    {
        $this->x = $newX;
        $this->y = $newY;
    }

    /**
     * function that returns both the X and Y values
     * @return type returns an array that contains the X and Y values
     * index 0: X
     * index 1: Y
     */
    public function getLocation()
    {
        return array($this->x, $this->y);
    }

    //</editor-fold>
    //<editor-fold desc="Parent Accessors" defaultstate="collapsed">
    /**
     * function that changest the parent DFD of this element
     * @param String $newParent
     */
    public function setParent($newParent)
    {
        $this->parent = $newParent;
    }

    /**
     * function that retrieves the UUID of the current parent of this element
     * @return String
     */
    public function getParent()
    {
        return $this->parent;
    }
    //</editor-fold>
    //<editor-fold desc="AssociativeArray Accessors" defaultstate="collapsed">
    /**
     * Returns an assocative array representing the element object. This 
     * assocative array has the following elements and types:
     * id String
     * label String
     * originator String
     * organization String
     * type String 
     * x Int
     * y Int
     * parent String
     * 
     * @return Mixed[]
     */
    public function getAssociativeArray()
    {
        // Get Entity array
        $elementArray = parent::getAssociativeArray();

        // Add Entity attributes to entity array
        $elementArray['x'] = $this->x;
        $elementArray['y'] = $this->y;
        $elementArray['parent'] = $this->parent;

        return $elementArray;
    }

    /**
     * Takes an assocative array representing an object and loads it into this
     * object.
     * @param Mixed[] $assocativeArray
     */
    public function loadAssociativeArray($associativeArray)
    {
        parent::loadAssociativeArray($associativeArray);
        if( isset($associativeArray['x']))
        {
            $this->x = $associativeArray['x'];
        }
        else
        {
            $this->x = 0;
        }
        if( isset($associativeArray['y']))
        {
            $this->y = $associativeArray['y'];
        }
        else
        {
            $this->y = 0;
        }
        if( isset($associativeArray['parent']))
        {
            $this->parent = $associativeArray['parent'];
        }
        else
        {
            //TODO - should this throw an exception?
            $this->parent = null;
        }
    }

    //</editor-fold>
    //</editor-fold>
}
?>