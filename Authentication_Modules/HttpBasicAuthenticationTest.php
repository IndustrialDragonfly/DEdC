<?php

/**
 * Uses Http Basic Authentication to verify the connecting client.
 * Unlike most classes for DEdC, this class currently can directly
 * communicate with the client in order to handle failed authentication (i.e.
 * allow multiple authentication attempts)
 *
 * @author eugene
 */
class HttpBasicAuthenticationTest implements Authenticatable
{
    /**
     *
     * @var String Username user supplied
     */
    protected $username;
    /**
     *
     * @var String Password user supplied
     */
    protected $password;
    /**
     *
     * @var ReadStorable Allows Datastore access
     */
    protected $storage;
    
    
    public function __construct($storage)
    {
        // TODO: Use filtering functions on Globals
        $this->username = $_SERVER['PHP_AUTH_USER'];
        $this->password = $_SERVER['PHP_AUTH_PW'];
        $this->storage = $storage;
    }
    
   public function authenticateClient()
   {
        if (!isset($this->username) ||  ($_POST['SeenBefore'] == 1 && $_POST['OldAuth'] == $this->username)) 
        {
            header('WWW-Authenticate: Basic realm="DEdC"');
            // If user hits cancel, goes to this next line
            $this->unauthorized();
            return false;
        } 
        else 
        {
            // Load user, hardcode organization
            $user = new User($this->storage, $this->username, "InD");
            if ($user->authenticate($this->password))
            {
                return true;
            }
            else
            {
                $this->unauthorized();
                return false;
            }
        }
   }
   
   // TODO: Make the reply more generic so that controller can handle it
   private function unauthorized()
   {
        header('HTTP/1.0 401 Unauthorized');
        echo "Unable to log you in.\n";
        
        echo "<form name=\"htmlform\" method=\"post\" action=\"Controller.php\">";
        
        echo "<input type='hidden' name='SeenBefore' value='1' />\n";
        if (isset($this->username))
        {
            echo "<input type='hidden' name='OldAuth' value=\"" . htmlspecialchars($this->username) . "\" />\n";
        }
        echo "<input type=\"submit\" value=\"Reauthenticate\">";
        
        echo "</form>";
   }
}
