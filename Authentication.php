<?php
/**
 * Not done yet (duh), stub function for checking authentication.
 * @return boolean
 */
   function authenticateUser()
   {
        if (!isset($_SERVER['PHP_AUTH_USER']) ||  ($_POST['SeenBefore'] == 1 && $_POST['OldAuth'] == $_SERVER['PHP_AUTH_USER'])) 
        {
            header('WWW-Authenticate: Basic realm="My Realm"');
            // If user hits cancel, goes to this next line
            unauthorized();
            return false;
        } 
        else 
        {
            if ($_SERVER['PHP_AUTH_USER'] == "Malcolm" && $_SERVER['PHP_AUTH_PW'] == "IndustrialDragonfly")
            {
                return true;
            }
            else
            {
                unauthorized();
                return false;
            }
        }
   }
   
   
   function unauthorized()
   {
        header('HTTP/1.0 401 Unauthorized');
        echo "Unable to log you in.\n";
        
        echo "<form name=\"htmlform\" method=\"post\" action=\"Controller.php\">";
        
        echo "<input type='hidden' name='SeenBefore' value='1' />\n";
        echo "<input type='hidden' name='OldAuth' value=\"" . htmlspecialchars($_SERVER['PHP_AUTH_USER']) . "\" />\n";
        echo "<input type=\"submit\" value=\"Reauthenticate\">";
        
        echo "</form>";
   }
?>