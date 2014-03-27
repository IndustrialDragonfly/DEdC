<?php

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
     * @var ID
     */
    protected $parent;

    //</editor-fold>
    //<editor-fold desc="Constructor" defaultstate="collapsed">
    /**
     *This is a constructor for the Element class.  This function always takes 
     * in 3 parameters, the first is always a valid storage medium and will be 
     * handled by the constructor for Entity. The second will be a user. The third 
     * paramenter will be either the UUID of the parent Diagram or will be an associative array.  
     * If the third parameter is an UUID the parent Diagram is set and this is 
     * treated like a null constructor, and will create an object with default 
     * values.  If the third parameter is an associative array it is passed to 
     * the Entity constructor for it to handle.
     * 
     * @param ReadStorable,WriteStorable $datastore
     * @param User $user
     * @param ID $id the UUID of the parent Diagram
     * 
     * or
     * 
     * @param ReadStorable,WriteStorable $datastore
     * @param User $user
     * @param Mixed[] $assocativeArray
     */
    public function __construct()
    {
        if(func_num_args() == 3)
        {
            // Third parameter is an ID
            if(is_a(func_get_arg(1), "ID"))
            {
                $this->ConstructElementWithDiagram(func_get_arg(0), func_get_arg(1), func_get_arg(2));
            }
            // If the second parmeter is an associative array pass it along to the Entity constructor
            else if(is_array(func_get_arg(2)))
            {
                parent::__construct(func_get_arg(0), func_get_arg(1), func_get_arg(2));
            }
            // If the second paramenter wasn't an ID or an array throw an exception
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
    
    /**
     * "Constructor" to create a new Element and associate it with a given array.
     * Element($storage, $user, $id)
     * @param Readable,Writable $storage
     * @param User $user
     * @param ID $id
     * @throws BadConstructorCallException
     */
    protected function ConstructElementWithDiagram($storage, $user, $id)
    {
        parent::__construct($storage, $user);
                $this->x = 0;
                $this->y = 0;
                // Find if the type of the second argument is an id of Diagram, if so, its a new node
                $type = $this->storage->getTypeFromUUID($id);
                if (is_subclass_of($type, "Diagram"))
                {
                    // TODO: Adder a getUserOwningID function to Storage bridge to do this in fewer queries
                    $Diagram = new $type($storage, $user, $id);
                    
                    // Check if user is authorized to access the Diagram before
                    // adding this element to the diagram.
                    if ($this->verifyUser($user, $Diagram->getUser()->getId()))
                    {
                        $this->parent = $id;
                    }
                    else
                    {
                        // TODO: Should throw an authorization exception
                        throw new BadConstructorCallException("The user is not authorized to perform this operation.");
                    }
                }
                else
                {
                    throw new BadConstructorCallException("The Id passed to the Element constructor was not valid Diagram");
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
        if($this->x != $newX)
        {
            $this->x = $newX;
            $this->update();
        }
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
        if($this->y != $newY)
        {
            $this->y = $newY;
            $this->update();
        }
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
        if( $this->x != $newX || $this->y != $newY)
        {
            $this->x = $newX;
            $this->y = $newY;
            $this->update();
        }
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
     * TODO - do some parameter checking to ensure that the parameter passed was an ID of a Diagram
     * @param String $newParent
     */
    public function setParent($newParent)
    {
        if($newParent == NULL)
        {
            throw new BadFunctionCallException("passed parent was null");
        }
        $type = $this->storage->getTypeFromUUID($newParent);
        if(is_subclass_of($type, "Diagram"))
        {
            if($this->parent != $newParent)
            {
                $this->parent = $newParent;
                $this->update();
            }
        }
        else
        {
            throw new BadFunctionCallException("ID passed to the setParent function did not belong to a valid Diagram Object!");
        }
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
     * id ID
     * label String
     * userId String
     * organization String
     * type String 
     * x Int
     * y Int
     * diagramId String
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
        $elementArray['diagramId'] = $this->parent;

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
        if( isset($associativeArray['diagramId']))
        {
            $this->parent = $associativeArray['diagramId'];
        }
        else
        {
            //TODO - should this throw an exception?
            $this->parent = null;
        }
    }
    
    public function setAssociativeArray($associativeArray)
    {
        $type = $this->storage->$associativeArray['diagramId'];
        // TODO: Adder a getUserOwningID function to Storage bridge to do this in fewer queries
        // Note that constructing an object will fail anyhow, the next step is for after this TODO is done
        $Diagram = new $type($this->storage, $this->user, $id);

        // Check if user is authorized to access the Diagram before
        // adding this element to the diagram.
        if ($this->verifyUser($this->user, $Diagram->getId()->getId()))
        {
            $this->loadAssociativeArray($associativeArray);
            $this->update();
        } 
        else
        {
            throw new BadFunctionCallException("User not authorized to access diagram to add element to.");
        }
    }

    //</editor-fold>
    //</editor-fold>
}
?>